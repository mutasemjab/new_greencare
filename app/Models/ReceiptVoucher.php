<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptVoucher extends Model
{
    protected $fillable = [
        'person_name', 'amount', 'receipt_date', 'payment_method', 'description', 'created_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:3',
        'receipt_date' => 'date',
    ];

    public const PAYMENT_METHODS = [
        'cash'          => 'نقداً',
        'cheque'        => 'شيك',
        'bank_transfer' => 'حوالة بنكية',
        'card'          => 'بطاقة',
    ];

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getNumberAttribute(): string
    {
        return str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    /** e.g. 150.250 → "فقط مائة وخمسون دينار أردني ومائتان وخمسون فلس لا غير" */
    public function getAmountInWordsAttribute(): string
    {
        $dinars = (int) floor((float) $this->amount);
        $fils   = (int) round(((float) $this->amount - $dinars) * 1000);

        $parts = [];
        if ($dinars > 0) {
            $parts[] = self::numberToArabicWords($dinars) . ' دينار أردني';
        }
        if ($fils > 0) {
            $parts[] = self::numberToArabicWords($fils) . ' فلس';
        }

        return 'فقط ' . ($parts ? implode(' و', $parts) : 'صفر دينار أردني') . ' لا غير';
    }

    public static function numberToArabicWords(int $number): string
    {
        if ($number === 0) {
            return 'صفر';
        }

        // [singular, dual, plural (3–10), accusative (11+)]
        $scales = [
            1000000000 => ['مليار', 'ملياران', 'مليارات', 'ملياراً'],
            1000000    => ['مليون', 'مليونان', 'ملايين', 'مليوناً'],
            1000       => ['ألف', 'ألفان', 'آلاف', 'ألفاً'],
        ];

        $words = [];

        foreach ($scales as $value => [$one, $two, $few, $many]) {
            $count = intdiv($number, $value);
            $number %= $value;

            if ($count === 0) {
                continue;
            }

            $words[] = match (true) {
                $count === 1                         => $one,
                $count === 2                         => $two,
                $count >= 3 && $count <= 10          => self::belowThousand($count) . ' ' . $few,
                default                              => self::belowThousand($count) . ' ' . $many,
            };
        }

        if ($number > 0) {
            $words[] = self::belowThousand($number);
        }

        return implode(' و', $words);
    }

    private static function belowThousand(int $n): string
    {
        $ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
        $teens = ['عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
        $tens = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
        $hundreds = ['', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

        $words = [];

        if ($h = intdiv($n, 100)) {
            $words[] = $hundreds[$h];
        }

        $rest = $n % 100;

        if ($rest >= 10 && $rest < 20) {
            $words[] = $teens[$rest - 10];
        } elseif ($rest > 0) {
            $unit = $rest % 10;
            $ten  = intdiv($rest, 10);
            // Arabic reads units before tens: "خمسة وعشرون"
            $words[] = trim(($unit ? $ones[$unit] : '') . ($unit && $ten ? ' و' : '') . ($ten ? $tens[$ten] : ''));
        }

        return implode(' و', $words);
    }
}
