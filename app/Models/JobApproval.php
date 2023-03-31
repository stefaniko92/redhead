<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApproval extends Model
{
    use HasFactory, SoftDeletes;

    const VOTE_APPROVED = 'APPROVED';
    const VOTE_REJECTED = 'REJECTED';

    protected $fillable = [
        'job_id',
        'approver_id',
        'vote'
    ];

   protected $casts = [
       'job_id' => 'integer',
       'approver_id' => 'integer',
       'vote' => 'string'
   ];

    /**
     * @return BelongsTo
     */
   public function job() : BelongsTo
   {
       return $this->belongsTo(Job::class);
   }

    /**
     * @return BelongsTo
     */
   public function approver() : BelongsTo
   {
       return $this->belongsTo(User::class, 'approver_id', 'id');
   }
}
