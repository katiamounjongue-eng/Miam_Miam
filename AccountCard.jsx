// AccountCard.jsx
import React from 'react';
import './AccountCard.css';
import logo from '../images/LogoZ.png';

function AccountCard({ account, isSelected, onSelect }) {
    return (
        <div
            className={`account-card-outer ${isSelected ? 'selected' : ''}`}
            onClick={() => onSelect(account)}
            role="button"
            tabIndex={0}
            onKeyDown={(e) => {
                if (e.key === 'Enter' || e.key === ' ') onSelect(account);
            }}
            aria-pressed={isSelected}
        >
            <div className="account-card">
                <div className="avatar">
                    {logo ? (
                        <img src={logo} alt={`${(account && account.name) || 'User'} logo`} />
                    ) : (
                        <div className="avatar-placeholder">
                            {((account && account.name) || 'U').slice(0, 1).toUpperCase()}
                        </div>
                    )}
                </div>

                <div className="account-content">
                    <div className="account-name">{account && account.name}</div>
                    <div className="account-email">{account && account.email}</div>
                </div>
            </div>
        </div>
    );
}

export default AccountCard;

/* Composant pour la carte "Créer un compte" */
export function CreateAccountCard({ onClick }) {
    return (
        <div
            className="create-outer"
            onClick={onClick}
            role="button"
            tabIndex={0}
            onKeyDown={(e) => {
                if (e.key === 'Enter' || e.key === ' ') onClick();
            }}
            aria-label="Créer un compte"
        >
            <div className="create-inner">
                <div className="create-plus" aria-hidden="true">
                    <span className="create-plus-sign">+</span>
                </div>
            </div>
        </div>
    );
}
