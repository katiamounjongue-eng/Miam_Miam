import React, { useState } from 'react';
import '../styles/settings.css';
import { useNavigate } from 'react-router-dom';
import logo from '../assets/images/logo-zeduc.png';
import { House, AlarmClockCheck, MessagesSquare, TrendingUp, UtensilsCrossed, Settings as SettingsIcon, UserCog, Bell, ChevronDown } from 'lucide-react';

function Settings() {
const navigate = useNavigate(); 

const handleNavigation = (page, path) => {
    setActivePage(page);
    navigate(path);
  };

  const [activePage, setActivePage] = useState('SETTINGS');
  const [openingHour, setOpeningHour] = useState('8');
  const [openingPeriod, setOpeningPeriod] = useState('AM');
  const [closingHour, setClosingHour] = useState('8');
  const [closingPeriod, setClosingPeriod] = useState('PM');

  const handleSave = () => {
    console.log({
      opening: `${openingHour} ${openingPeriod}`,
      closing: `${closingHour} ${closingPeriod}`
    });
    alert('Heures de travail enregistrées !');
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

      <div className="settings-content">
        {/* Header */}
        <div className="settings-header">
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
        <div className="settings-main-content">
          <div className="title-row">
            <h2>Heures de travail</h2>
            <button className="add-button" onClick={handleSave}>
              Ajouter un plat
            </button>
          </div>

          {/* Working Hours Section */}
          <div className="working-hours-container">
            <div className="time-field">
              <label className="time-label">heure d'ouverture</label>
              <div className="time-input-group">
                <input
                  type="number"
                  className="time-input"
                  value={openingHour}
                  onChange={(e) => setOpeningHour(e.target.value)}
                  min="1"
                  max="12"
                />
                <select
                  className="period-select"
                  value={openingPeriod}
                  onChange={(e) => setOpeningPeriod(e.target.value)}
                >
                  <option value="AM">AM</option>
                  <option value="PM">PM</option>
                </select>
              </div>
            </div>

            <div className="time-field">
              <label className="time-label">Heure de fermeture</label>
              <div className="time-input-group">
                <input
                  type="number"
                  className="time-input"
                  value={closingHour}
                  onChange={(e) => setClosingHour(e.target.value)}
                  min="1"
                  max="12"
                />
                <select
                  className="period-select"
                  value={closingPeriod}
                  onChange={(e) => setClosingPeriod(e.target.value)}
                >
                  <option value="AM">AM</option>
                  <option value="PM">PM</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}

export default Settings;
