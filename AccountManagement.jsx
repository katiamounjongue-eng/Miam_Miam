// src/pages/AccountManagement.jsx
import React, { useState } from 'react';
import AccountCard, { CreateAccountCard } from '../components/AccountCard';
import AccountDetails from '../components/AccountDetails';
import './AccountManagement.css';

const initialAccounts = [
    { id: 1, name: 'Nziko joel Bouyim', email: 'joel.bouyim@2029.ucac-icam.com', createdAt: '2024-01-15', role: 'Admin' },
    { id: 2, name: 'Bob Johnson', email: 'bob@example.com', createdAt: '2024-03-22', role: 'Editor' },
    { id: 3, name: 'Charlie Brown', email: 'charlie@example.com', createdAt: '2024-06-05', role: 'Viewer' },
];

function AccountManagement() {
    const [accounts, setAccounts] = useState(initialAccounts);
    const [selectedAccount, setSelectedAccount] = useState(initialAccounts[0] || null);

    const handleDelete = (id) => {
        setAccounts(prev => prev.filter(acc => acc.id !== id));
        setSelectedAccount(prev => (prev && prev.id === id ? null : prev));
    };

    const handleEdit = (account) => {
        console.log('Modifier le compte:', account);
        // Ici tu peux ouvrir un modal / formulaire pour éditer et mettre à jour `accounts`
    };

    const handleCreate = () => {
        const newAccount = {
            id: accounts.length + 1,
            name: 'New User',
            email: `newuser${accounts.length + 1}@example.com`,
            createdAt: new Date().toISOString().split('T')[0],
            role: 'User',
        };
        setAccounts(prev => [...prev, newAccount]);
        setSelectedAccount(newAccount);
    };

    return (
        <div className="accounts-page">
            <div className="accounts-left">
                <div className="accounts-header">
                    <h2>Comptes</h2>
                </div>

                <div className="accounts-list">
                    {accounts.map(acc => (
                        <AccountCard
                            key={acc.id}
                            account={acc}
                            isSelected={selectedAccount?.id === acc.id}
                            onSelect={setSelectedAccount}
                        />
                    ))}
                    <CreateAccountCard onClick={handleCreate} />
                </div>
            </div>

            <div className="accounts-right">
                {selectedAccount ? (
                    <AccountDetails
                        account={selectedAccount}
                        onDelete={handleDelete}
                        onEdit={handleEdit}
                    />
                ) : (
                    <div className="no-selection">Aucun compte sélectionné.</div>
                )}
            </div>
        </div>
    );
}

export default AccountManagement;
