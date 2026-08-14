<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisplayNoteTransfer;
use Illuminate\Http\Request;

class DisplayNoteTransferController extends Controller
{
    public function edit()
    {
        $note = DisplayNoteTransfer::first() ?? new DisplayNoteTransfer();

        return view('admin.transfers.note', compact('note'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name_en' => 'required|string',
            'name_ar' => 'required|string',
        ]);

        $note = DisplayNoteTransfer::first();

        if ($note) {
            $note->update($data);
        } else {
            DisplayNoteTransfer::create($data);
        }

        return redirect()->route('admin.transfers.note.edit')
            ->with('success', 'تم حفظ الملاحظة بنجاح');
    }
}
