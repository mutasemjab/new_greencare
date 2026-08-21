<?php

namespace App\Services;

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Contract\Firestore as FirebaseFirestore;
use Throwable;

class FirebaseService
{
    private ?FirebaseAuth $auth = null;
    private ?FirebaseFirestore $firestore = null;
    private bool $configured;

    public function __construct()
    {
        $this->configured = is_string(config('firebase.credentials'))
            && file_exists(config('firebase.credentials'));
    }

    /**
     * Mints a Firebase custom token for this user, so the mobile app can
     * sign into Firebase Auth and have request.auth.uid (the Laravel user
     * id, as a string) trusted by the Firestore security rules.
     *
     * Returns null (never throws) when Firebase isn't configured yet, or
     * on any SDK failure — callers should treat null as "unavailable".
     */
    public function mintCustomToken(User $user): ?string
    {
        try {
            return $this->auth()?->createCustomToken((string) $user->id)->toString();
        } catch (Throwable $e) {
            Log::error('Firebase: failed to mint custom token', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Creates the room's chatRooms/{id} Firestore document and returns the
     * document id to store as rooms.firebase_room_id. Falls back to a
     * locally-generated id (Firestore doc is simply skipped) when Firebase
     * isn't configured — room creation must never fail because of this.
     */
    public function createRoomDocument(Room $room): string
    {
        $firebaseRoomId = 'room_' . uniqid();

        try {
            $firestore = $this->firestore();

            if ($firestore) {
                $firestore->database()->collection('chatRooms')->document($firebaseRoomId)->set([
                    'members'              => $room->memberUserIds(),
                    'last_message_preview' => null,
                    'last_message_at'      => null,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Firebase: failed to create room document', ['room_id' => $room->id, 'error' => $e->getMessage()]);
        }

        return $firebaseRoomId;
    }

    /**
     * Re-syncs the room's Firestore `members` array after a membership
     * change (add/remove member, etc). Best-effort — logs and returns on
     * any failure, never throws.
     */
    public function syncRoomMembers(Room $room): void
    {
        if (! $room->firebase_room_id) {
            return;
        }

        try {
            $firestore = $this->firestore();

            if ($firestore) {
                $firestore->database()->collection('chatRooms')->document($room->firebase_room_id)->set([
                    'members' => $room->memberUserIds(),
                ], ['merge' => true]);
            }
        } catch (Throwable $e) {
            Log::error('Firebase: failed to sync room members', ['room_id' => $room->id, 'error' => $e->getMessage()]);
        }
    }

    private function auth(): ?FirebaseAuth
    {
        if (! $this->configured) {
            return null;
        }

        return $this->auth ??= $this->factory()->createAuth();
    }

    private function firestore(): ?FirebaseFirestore
    {
        if (! $this->configured) {
            return null;
        }

        return $this->firestore ??= $this->factory()->createFirestore();
    }

    private function factory(): Factory
    {
        return (new Factory())->withServiceAccount(config('firebase.credentials'));
    }
}
