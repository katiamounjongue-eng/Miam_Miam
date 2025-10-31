// AccountDetails.jsx
import React from 'react';
import './AccountDetails.css';
import logo from '../images/LogoZ.png'; // adapte le chemin si nécessaire

export default function AccountDetails({ account, onDelete, onEdit }) {
    if (!account) {
        return (
            <aside className="account-details-card" aria-label="Détails du compte">
                <div className="ad-empty">Aucun compte sélectionné</div>
            </aside>
        );
    }

    return (
        <aside className="account-details-card" aria-label="Détails du compte">
            <div className="ad-header">
                <img src={logo} alt="Logo" className="ad-logo" />
                <div className="ad-header-text">
                    <h3 className="ad-name">{account.name}</h3>
                    <div className="ad-email">{account.email}</div>
                </div>
            </div>

            <div className="ad-content">
                <div className="ad-field">
                    <div className="ad-field-label">Date de création</div>
                    <div className="ad-field-value">{account.createdAt}</div>
                    <div className="ad-divider" />
                </div>

                <div className="ad-field">
                    <div className="ad-field-label">Nom du propriétaire</div>
                    <div className="ad-field-value">
                        <strong>{account.name}</strong>
                    </div>
                    <div className="ad-divider" />
                </div>

                <div className="ad-field">
                    <div className="ad-field-label">Role</div>
                    <div className="ad-field-value">{account.role}</div>
                    <div className="ad-divider" />
                </div>
            </div>

            <div className="ad-actions">
                <button
                    className="ad-btn ad-btn-danger"
                    onClick={() => onDelete && onDelete(account.id)}
                    aria-label="Supprimer le compte"
                >
                    Supprimer le compte
                </button>

                <button
                    className="ad-btn ad-btn-edit"
                    onClick={() => onEdit && onEdit(account)}
                    aria-label="Modifier le compte"
                >
                    Modifier le compte
                </button>
            </div>
        </aside>
    );
}
