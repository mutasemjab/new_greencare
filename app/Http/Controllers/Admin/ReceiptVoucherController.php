<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReceiptVoucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReceiptVoucherController extends Controller
{
    private function rules(): array
    {
        return [
            'person_name'    => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.001|max:999999999',
            'receipt_date'   => 'required|date',
            'payment_method' => ['required', Rule::in(array_keys(ReceiptVoucher::PAYMENT_METHODS))],
            'description'    => 'nullable|string|max:255',
        ];
    }

    public function index(Request $request)
    {
        if (!auth()->user()->can('receipt-table')) {
            abort(403);
        }

        $query = ReceiptVoucher::with('createdBy')->latest('receipt_date')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('person_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");

                // Voucher number search, e.g. "000123" or "123"
                if (ctype_digit($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('receipt_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('receipt_date', '<=', $request->to);
        }

        $total    = (clone $query)->sum('amount');
        $receipts = $query->paginate(20)->withQueryString();

        return view('admin.receipts.index', compact('receipts', 'total'));
    }

    public function create()
    {
        if (!auth()->user()->can('receipt-add')) {
            abort(403);
        }

        return view('admin.receipts.form', ['receipt' => new ReceiptVoucher(['receipt_date' => now(), 'payment_method' => 'cash'])]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('receipt-add')) {
            abort(403);
        }

        $receipt = ReceiptVoucher::create($request->validate($this->rules()) + [
            'created_by' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.receipts.show', $receipt)
            ->with('success', "تم حفظ سند القبض رقم {$receipt->number}");
    }

    /** Printable voucher page. */
    public function show(ReceiptVoucher $receipt)
    {
        if (!auth()->user()->can('receipt-table')) {
            abort(403);
        }

        $receipt->load('createdBy');

        return view('admin.receipts.print', compact('receipt'));
    }

    public function edit(ReceiptVoucher $receipt)
    {
        if (!auth()->user()->can('receipt-edit')) {
            abort(403);
        }

        return view('admin.receipts.form', compact('receipt'));
    }

    public function update(Request $request, ReceiptVoucher $receipt)
    {
        if (!auth()->user()->can('receipt-edit')) {
            abort(403);
        }

        $receipt->update($request->validate($this->rules()));

        return redirect()->route('admin.receipts.show', $receipt)
            ->with('success', "تم تعديل سند القبض رقم {$receipt->number}");
    }

    public function destroy(ReceiptVoucher $receipt)
    {
        if (!auth()->user()->can('receipt-delete')) {
            abort(403);
        }

        $number = $receipt->number;
        $receipt->delete();

        return redirect()->route('admin.receipts.index')
            ->with('success', "تم حذف سند القبض رقم {$number}");
    }
}
