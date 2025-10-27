<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';
    protected $primaryKey = 'complaint_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'complaint_id',
        'user_id',
        'order_id',
        'complaint_type',
        'subject',
        'description',
        'priority',
        'status',
        'resolution_note',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation : appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }

    /**
     * Relation : peut être liée à une commande
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    /**
     * Scope pour les réclamations en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les réclamations résolues
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope pour les réclamations urgentes
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Obtenir le label du type
     */
    public function getTypeLabelAttribute()
    {
        return match($this->complaint_type) {
            'order' => 'Problème de commande',
            'delivery' => 'Problème de livraison',
            'quality' => 'Problème de qualité',
            'payment' => 'Problème de paiement',
            'technical' => 'Problème technique',
            'other' => 'Autre',
        };
    }

    /**
     * Obtenir le label du statut
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'resolved' => 'Résolu',
            'closed' => 'Fermé',
        };
    }

    /**
     * Vérifier si la réclamation est résolue
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved' || $this->status === 'closed';
    }

    /**
     * Calculer le temps de résolution en heures
     */
    public function getResolutionTimeAttribute()
    {
        if (!$this->resolved_at) {
            return null;
        }
        return $this->created_at->diffInHours($this->resolved_at);
    }
}
