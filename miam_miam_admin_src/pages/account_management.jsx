import React, { useState } from 'react';
import '../styles/account_management.css';
import logo from '../assets/images/logo-zeduc.png';
import { useNavigate } from 'react-router-dom';
import { House, AlarmClockCheck, MessagesSquare, TrendingUp, UtensilsCrossed, Settings, UserCog, Bell, ChevronDown, Plus } from 'lucide-react';

function AccountManagement() {
  const navigate = useNavigate();

  const handleNavigation = (page, path) => {
    setActivePage(page);
    navigate(path);
  };
  
  const [activePage, setActivePage] = useState('ACCOUNT MANAGEMENT');
  const [selectedAccount, setSelectedAccount] = useState(null);

  const accounts = [
    {
      id: 1,
      name: 'Nziko joel Bouyim',
      email: 'joel.bouyim2025.iudc-bam.com',
      creationDate: '02/07/2025',
      role: 'Gérant'
    },
    {
      id: 2,
      name: 'Nziko joel Bouyim',
      email: 'joel.bouyim2025.iudc-bam.com',
      creationDate: '02/07/2025',
      role: 'Gérant'
    },
    {
      id: 3,
      name: 'Nziko joel Bouyim',
      email: 'joel.bouyim2025.iudc-bam.com',
      creationDate: '02/07/2025',
      role: 'Gérant'
    }
  ];

  const handleSelectAccount = (account) => {
    setSelectedAccount(account);
  };

  const handleDeleteAccount = () => {
    if (selectedAccount) {
      console.log('Supprimer le compte:', selectedAccount);
      alert(`Compte ${selectedAccount.name} supprimé !`);
      setSelectedAccount(null);
    }
  };

  const handleModifyAccount = () => {
    if (selectedAccount) {
      console.log('Modifier le compte:', selectedAccount);
      alert(`Modifier le compte ${selectedAccount.name}`);
    }
  };

  const handleCreateAccount = () => {
    console.log('Créer un nouveau compte');
    alert('Créer un nouveau compte');
  };

  return (
    <>
      <div className="sidebar">
        <div className="logo-container">
          <img src={logo} alt="Zeduc Space Logo" className="logo"/>
        </div>
        
        <div 
          className={`nav-link ${activePage === 'HOME' ? 'active' : ''}`}
          onClick={() => handleNavigation('HOME', '/admin')}
          style={{ cursor: 'pointer' }}
        >
          <House size={18} color={activePage === 'HOME' ? '#D4A574' : '#ffffff'} />
          HOME
        </div>

        <div 
          className={`nav-link ${activePage === 'ORDER HISTORY' ? 'active' : ''}`}
          onClick={() => handleNavigation('ORDER HISTORY', '/order_history')}
          style={{ cursor: 'pointer' }}
        >
          <AlarmClockCheck size={18} color={activePage === 'ORDER HISTORY' ? '#D4A574' : '#ffffff'} />
          ORDER HISTORY
        </div>

        <div 
          className={`nav-link ${activePage === 'MESSAGES' ? 'active' : ''}`}
          onClick={() => handleNavigation('MESSAGES', '/messages_admin')}
          style={{ cursor: 'pointer' }}
        >
          <MessagesSquare size={18} color={activePage === 'MESSAGES' ? '#D4A574' : '#ffffff'} />
          MESSAGES
        </div>

        <div 
          className={`nav-link ${activePage === 'STATISTICS' ? 'active' : ''}`}
          onClick={() => handleNavigation('STATISTICS', '/admin_statistics')}
          style={{ cursor: 'pointer' }}
        >
          <TrendingUp size={18} color={activePage === 'STATISTICS' ? '#D4A574' : '#ffffff'} />
          STATISTICS
        </div>
        
        <div 
          className={`nav-link ${activePage === 'PRODUCTS' ? 'active' : ''}`}
          onClick={() => handleNavigation('PRODUCTS', '/products')}
          style={{ cursor: 'pointer' }}
        >
          <UtensilsCrossed size={18} color={activePage === 'PRODUCTS' ? '#D4A574' : '#ffffff'} />
          PRODUCTS
        </div>
        
        <div 
          className={`nav-link ${activePage === 'SETTINGS' ? 'active' : ''}`}
          onClick={() => handleNavigation('SETTINGS', '/settings')}
          style={{ cursor: 'pointer' }}
        >
          <Settings size={18} color={activePage === 'SETTINGS' ? '#D4A574' : '#ffffff'} />
          SETTINGS
        </div>
        
        <div 
          className={`nav-link ${activePage === 'ACCOUNT MANAGEMENT' ? 'active' : ''}`}
          onClick={() => handleNavigation('ACCOUNT MANAGEMENT', '/account_management')}
          style={{ cursor: 'pointer' }}
        >
          <UserCog size={18} color={activePage === 'ACCOUNT MANAGEMENT' ? '#D4A574' : '#ffffff'} />
          ACCOUNT MANAGEMENT
        </div>

        <div className="restaurant-status">
          <div className="status-dot"></div>
          <span>Restaurant Open</span>
        </div>
      </div>

      <div className="account-management-content">
        {/* Header */}
        <div className="account-management-header">
          <div className="header-left">
            <h1>Zeduc-Sp@ce is open</h1>
            <p className="header-date">22 Mai 2021, 12:21 PM</p>
          </div>
          <div className="user-info">
            <span>Admin1_resto</span>
            <div className="user-avatar">A</div>
            <ChevronDown className="chevron-down" size={20} />
            <Bell className="notification-bell" size={20} />
          </div>
        </div>

        {/* Main Content */}
        <div className="account-management-main-content">
          {/* Left Section - Accounts List */}
          <div className="accounts-section">
            <h2 className="section-title">Comptes</h2>
            
            <div className="accounts-list">
              {accounts.map((account) => (
                <div 
                  key={account.id} 
                  className={`account-card ${selectedAccount?.id === account.id ? 'selected' : ''}`}
                  onClick={() => handleSelectAccount(account)}
                >
                  <div className="account-card-logo">
                    <svg viewBox="0 0 120 100" className="account-logo">
                      <circle cx="60" cy="45" r="35" fill="none" stroke="#D4A574" strokeWidth="1.5"/>
                      <path d="M 30 40 Q 35 37 40 40" fill="none" stroke="#D4A574" strokeWidth="1"/>
                      <path d="M 80 40 Q 85 37 90 40" fill="none" stroke="#D4A574" strokeWidth="1"/>
                      <text x="60" y="47" textAnchor="middle" fill="#D4A574" fontSize="24" fontFamily="serif">HL</text>
                      <text x="60" y="65" textAnchor="middle" fill="#D4A574" fontSize="7" fontFamily="Arial" letterSpacing="1">ZEDUC-SPACE</text>
                      <line x1="35" y1="68" x2="85" y2="68" stroke="#D4A574" strokeWidth="0.5"/>
                      <text x="60" y="74" textAnchor="middle" fill="#D4A574" fontSize="5">RESTAURANT • COFFEE • LOUNGE</text>
                    </svg>
                  </div>
                  <div className="account-card-info">
                    <h3 className="account-name">{account.name}</h3>
                    <p className="account-email">{account.email}</p>
                  </div>
                </div>
              ))}

              <button className="create-account-button" onClick={handleCreateAccount}>
                <div className="create-icon-wrapper">
                  <Plus size={24} color="#ffa500" />
                </div>
                <span>Créer un compte</span>
              </button>
            </div>
          </div>

          {/* Right Section - Account Details */}
          {selectedAccount && (
            <div className="account-details-section">
              <div className="account-details-header">
                <div className="details-logo">
                  <svg viewBox="0 0 120 100" className="details-logo-svg">
                    <circle cx="60" cy="45" r="35" fill="none" stroke="#D4A574" strokeWidth="1.5"/>
                    <path d="M 30 40 Q 35 37 40 40" fill="none" stroke="#D4A574" strokeWidth="1"/>
                    <path d="M 80 40 Q 85 37 90 40" fill="none" stroke="#D4A574" strokeWidth="1"/>
                    <text x="60" y="47" textAnchor="middle" fill="#D4A574" fontSize="24" fontFamily="serif">HL</text>
                    <text x="60" y="65" textAnchor="middle" fill="#D4A574" fontSize="7" fontFamily="Arial" letterSpacing="1">ZEDUC-SPACE</text>
                    <line x1="35" y1="68" x2="85" y2="68" stroke="#D4A574" strokeWidth="0.5"/>
                    <text x="60" y="74" textAnchor="middle" fill="#D4A574" fontSize="5">RESTAURANT • COFFEE • LOUNGE</text>
                  </svg>
                </div>
                <div className="details-header-info">
                  <h3 className="details-name">{selectedAccount.name}</h3>
                  <p className="details-email">{selectedAccount.email}</p>
                </div>
              </div>

              <div className="account-details-body">
                <div className="detail-field">
                  <label>Date de création</label>
                  <div className="detail-value">{selectedAccount.creationDate}</div>
                </div>

                <div className="detail-field">
                  <label>Nom du propriétaire</label>
                  <div className="detail-value">{selectedAccount.name}</div>
                </div>

                <div className="detail-field">
                  <label>Rôle</label>
                  <div className="detail-value">{selectedAccount.role}</div>
                </div>
              </div>

              <div className="account-details-actions">
                <button className="delete-button" onClick={handleDeleteAccount}>
                  Supprimer le compte
                </button>
                <button className="modify-button" onClick={handleModifyAccount}>
                  Modifier le compte
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  );
}

export default AccountManagement;
