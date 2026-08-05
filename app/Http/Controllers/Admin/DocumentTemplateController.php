<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function editAuthorization()
    {
        $document = DocumentTemplate::forType('authorization');

        return view('admin.sihati.documents.edit', compact('document'));
    }

    public function updateAuthorization(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DocumentTemplate::forType('authorization')->update($data + ['updated_by' => auth('admin')->id()]);

        return redirect()->route('admin.sihati.documents.authorization')
            ->with('success', 'تم حفظ وثيقة التفويض بنجاح');
    }

    public function editPledge()
    {
        $document = DocumentTemplate::forType('pledge');

        return view('admin.sihati.documents.edit', compact('document'));
    }

    public function updatePledge(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DocumentTemplate::forType('pledge')->update($data + ['updated_by' => auth('admin')->id()]);

        return redirect()->route('admin.sihati.documents.pledge')
            ->with('success', 'تم حفظ وثيقة التعهد بنجاح');
    }
}
