import React, { useState } from "react";
import logo from "../assets/images/logo-zeduc.png";
import { useNavigate } from "react-router-dom";
import "../styles/admin_statistics.css";
import {
  House,
  AlarmClockCheck,
  MessagesSquare,
  TrendingUp,
  UtensilsCrossed,
  Settings,
  UserCog,
  Bell,
  ChevronDown,
} from "lucide-react";

function AdminStatistics() {
  const navigate = useNavigate();
  const [activePage, setActivePage] = useState("STATISTICS");

  // Fonction de navigation centralisée
  const handleNavigation = (path, pageName) => {
    setActivePage(pageName);
    navigate(path);
  };

  const chartData = [
    { day: "Sunday", value: 18000, label: "Sund 17" },
    { day: "Monday", value: 21000, label: "Mond 18" },
    { day: "Tuesday", value: 26000, label: "Tuesd 19" },
    { day: "Wednesday", value: 30000, label: "Wedn 20" },
    { day: "Thursday", value: 27000, label: "Thurs 21" },
    { day: "Friday", value: 22000, label: "Frid 22" },
    { day: "Saturday", value: 20000, label: "Saturd 23" },
  ];

  const maxValue = 40000;

  const topSelling = [
    "Okok sucré",
    "Eru",
    "Ndole",
    "Poulet braisé",
    "Poulet pané",
    "Koki",
    "Riz Jollof",
    "Mbongo Tchobi",
    "Sauce jaune",
    "Oko salé",
  ];

  return (
    <>
       <div className="sidebar">
        <div class="logo-container">
            <img src={logo} alt="Zeduc Space Logo" class="logo"/>
        </div>
        
        <a 
  href="/admin" 
  className={`nav-link ${activePage === 'HOME' ? 'active' : ''}`}
  onClick={() => setActivePage('HOME')}
>
  <House size={18} color={activePage === 'HOME' ? '#D4A574' : '#ffffff'} />
  HOME
</a>

<a 
  href="/order_history" 
  className={`nav-link ${activePage === 'ORDER HISTORY' ? 'active' : ''}`}
  onClick={() => setActivePage('ORDER HISTORY')}
>
  <AlarmClockCheck size={18} color={activePage === 'ORDER HISTORY' ? '#D4A574' : '#ffffff'} />
  ORDER HISTORY
</a>

<a 
  href="/messages_admin" 
  className={`nav-link ${activePage === 'MESSAGES' ? 'active' : ''}`}
  onClick={() => setActivePage('MESSAGES')}
>
  <MessagesSquare size={18} color={activePage === 'MESSAGES' ? '#D4A574' : '#ffffff'} />
  MESSAGES
</a>

<a 
  href="/admin_statistics" 
  className={`nav-link ${activePage === 'STATISTICS' ? 'active' : ''}`}
  onClick={() => setActivePage('STATISTICS')}
>
  <TrendingUp size={18} color={activePage === 'STATISTICS' ? '#D4A574' : '#ffffff'} />
  STATISTICS
</a>
        
        <a 
          href="/products" 
          className={`nav-link ${activePage === 'PRODUCTS' ? 'active' : ''}`}
          onClick={() => setActivePage('PRODUCTS')}
        >
          <UtensilsCrossed size={18} color={activePage === 'PRODUCTS' ? '#D4A574' : '#ffffff'} />
          PRODUCTS
        </a>
        
        <a 
          href="/settings" 
          className={`nav-link ${activePage === 'SETTINGS' ? 'active' : ''}`}
          onClick={() => setActivePage('SETTINGS')}
        >
          <Settings size={18} color={activePage === 'SETTINGS' ? '#D4A574' : '#ffffff'} />
          SETTINGS
        </a>
        
        <a 
          href="/account_management" 
          className={`nav-link ${activePage === 'ACCOUNT MANAGEMENT' ? 'active' : ''}`}
          onClick={() => setActivePage('ACCOUNT MANAGEMENT')}
        >
          <UserCog size={18} color={activePage === 'ACCOUNT MANAGEMENT' ? '#D4A574' : '#ffffff'} />
          ACCOUNT MANAGEMENT
        </a>

        <div className="restaurant-status">
          <div className="status-dot"></div>
          <span>Restaurant Open</span>
        </div>
      </div>


      {/* Contenu principal */}
      <div className="stats-content">
        <div className="stats-header">
          <div className="header-left">
            <h1>Zeduc-Sp@ce</h1>
            <p className="header-date">22 Octobre 2025 - 12:21 PM | Douala</p>
          </div>
          <div className="user-info">
            <span>Admin1_resto</span>
            <div className="user-avatar">A</div>
            <ChevronDown className="chevron-down" size={20} />
            <Bell className="notification-bell" size={20} />
          </div>
        </div>

        <div className="stats-main-content">
          {/* Cartes statistiques */}
          <div className="stats-cards-row">
            <div className="stat-card">
              <p className="stat-label">Revenue totale</p>
              <h2 className="stat-value">34 550F</h2>
            </div>
            <div className="stat-card">
              <p className="stat-label">Commandes Acceptées</p>
              <div className="acceptance-stats">
                <h2 className="stat-value">41/42</h2>
                <span className="percentage">97%</span>
              </div>
            </div>
            <div className="stat-card">
              <p className="stat-label">Heures (Ouverture)</p>
              <h2 className="stat-value">8AM - 8PM</h2>
            </div>
          </div>

          {/* Graphique + Liste */}
          <div className="charts-row">
            <div className="presentation-card">
              <div className="card-header">
                <h3>Présentation</h3>
                <button className="week-button">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <rect x="2" y="4" width="4" height="10" fill="currentColor" />
                    <rect x="10" y="2" width="4" height="12" fill="currentColor" />
                  </svg>
                  Week
                  <ChevronDown size={14} />
                </button>
              </div>

              <div className="chart-container">
                <div className="chart-y-axis">
                  <span>4000F</span>
                  <span>3000F</span>
                  <span>2000F</span>
                  <span>1000F</span>
                  <span>500F</span>
                  <span>0</span>
                </div>

                <div className="chart-bars">
                  {chartData.map((item, index) => (
                    <div key={index} className="bar-wrapper">
                      <div className="bar-column">
                        <div
                          className="bar"
                          style={{ height: `${(item.value / maxValue) * 100}%` }}
                        ></div>
                      </div>
                      <span className="bar-label">{item.label}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Top Selling */}
            <div className="top-selling-card">
              <h3>Top-selling</h3>
              <div className="top-selling-list">
                {topSelling.map((item, index) => (
                  <div key={index} className="top-selling-item">
                    <span className="item-number">{index + 1}</span>
                    <span className="item-name">{item}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}

export default AdminStatistics;
