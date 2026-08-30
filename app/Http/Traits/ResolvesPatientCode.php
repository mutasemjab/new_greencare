<?php

namespace App\Http\Traits;

use App\Models\Room;
use App\Models\User;
use App\Models\VisitForm;

trait ResolvesPatientCode
{
    /**
     * Resolves a submitted code against both code namespaces: Sihati room
     * codes (patient_code) and medical-visit-form codes (code). The two
     * are independent tables, never both matched at once.
     *
     * Returns [Room|null, VisitForm|null, string|null $error]. On success
     * exactly one of Room/VisitForm is set (or both null when the code is
     * empty/unrecognized — silently no discount, not an error). $error is
     * set only when a matching code was found but doesn't belong to this
     * user, so the caller can turn it into a 403.
     */
    protected function resolveCodeSource(?string $code, User $user): array
    {
        if (! $code) {
            return [null, null, null];
        }

        $room = Room::where('patient_code', $code)->first();

        if ($room) {
            return $room->hasMember($user)
                ? [$room, null, null]
                : [null, null, 'هذا الكود غير مرتبط بحسابك'];
        }

        $visitForm = VisitForm::where('code', $code)->first();

        if ($visitForm) {
            return $visitForm->hasMember($user)
                ? [null, $visitForm, null]
                : [null, null, 'هذا الكود غير مرتبط بحسابك'];
        }

        return [null, null, null];
    }
}
