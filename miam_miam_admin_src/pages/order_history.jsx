import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import "../styles/order_history.css";
import logo from "../assets/images/logo-zeduc.png";
import {
  House,
  AlarmClockCheck,
  MessagesSquare,
  TrendingUp,
  UtensilsCrossed,
  Settings,
  UserCog,
} from "lucide-react";

function OrderHistory() {
  const navigate = useNavigate();
  const [activePage, setActivePage] = useState("ORDER HISTORY");

  // Fonction pour naviguer et changer l’état actif
  const handleNavigation = (path, pageName) => {
    setActivePage(pageName);
    navigate(path);
  };

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
          href="#" 
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


      <div className="content">
        <div className="header">
          <input type="text" placeholder="Search" />
          <div>
            Admin | Jesto <span>A</span> | <span>🔔</span>
          </div>
        </div>

        <h2>Historique des commandes</h2>

        <div className="filter">
            <button>Filter Order</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>ID</th>
                    <th>Menu</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jeremy Passion</td>
                    <td>#214</td>
                    <td>Okok sucré(2)</td>
                    <td>26 Oct 2025, 12:01 PM</td>
                    <td><button>Pending</button></td>
                </tr>
                <tr>
                    <td>Auriane</td>
                    <td>#210</td>
                    <td>Eru (2)</td>
                    <td>25 Oct 2025, 11:34 PM</td>
                    <td><button>Selected</button></td>
                </tr>
                <tr>
                    <td>Katia</td>
                    <td>#212</td>
                    <td>Ndole(2), Eru (1)</td>
                    <td>24 Oct 2025, 11:40 PM</td>
                    <td><button>Preparation</button></td>
                </tr>
                <tr>
                    <td>Joel200</td>
                    <td>#213</td>
                    <td>Poulet pané(1), poulet braisé(1)</td>
                    <td>24 Oct 2025, 11:51 PM</td>
                    <td><button>En Route</button></td>
                </tr>
                <tr>
                    <td>Chembou</td>
                    <td>#211</td>
                    <td>Riz Jollof (2)</td>
                    <td>24 Oct 2025, 12:01 PM</td>
                    <td><button>Completed</button></td>
                </tr>
                <tr>
                    <td>JeminaK</td>
                    <td>#207</td>
                    <td>Koki (2)</td>
                    <td>23 Oct 2025, 10:41 PM</td>
                    <td><button>Cancelled</button></td>
                </tr>
                <tr>
                    <td>Daniel400</td>
                    <td>#206</td>
                    <td>Sauce jaune(4)</td>
                    <td>23 Oct 2025, 10:37 PM</td>
                    <td><button>Completed</button></td>
                </tr>
                <tr>
                    <td>Messie</td>
                    <td>#205</td>
                    <td>Top(2)</td>
                    <td>22 Oct 2025, 10:31 PM</td>
                    <td><button>Completed</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    </>
  );
}

export default OrderHistory;
