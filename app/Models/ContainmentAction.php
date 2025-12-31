<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;     // Để dùng xóa mềm
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Để tự động tạo UUID

class ContainmentAction extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    // Khai báo tên bảng (nếu cần thiết, dù Laravel tự đoán được)
    protected $table = 'containment_actions';

    // Các cột được phép thêm/sửa (Mass Assignment)
    protected $fillable = [
        'complaint_id',
        'action_content',
        'person_in_charge',
        'due_date',
        'status',          // Pending, In_Progress, Completed
        'completion_date'
    ];
}