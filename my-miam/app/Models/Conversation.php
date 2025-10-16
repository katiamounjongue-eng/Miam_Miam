<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'last_message_by',
		'subject',
		'is_read_customer',
		'is_read_support',
	];

	protected $casts = [
		'is_read_customer' => 'boolean',
		'is_read_support' => 'boolean',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function messages()
	{
		return $this->hasMany(Message::class)->latest();
	}
}

