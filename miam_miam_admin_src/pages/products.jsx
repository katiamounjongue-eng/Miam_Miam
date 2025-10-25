import React, { useState } from 'react';
import { useNavigate } from "react-router-dom";
import '../styles/products.css';
import logo from '../assets/images/logo-zeduc.png';
import ImageWithFallback from "../components/ImageWithFallback";
import { House, AlarmClockCheck, MessagesSquare, TrendingUp, UtensilsCrossed, Settings, UserCog, Bell, ChevronDown, Plus, Star } from 'lucide-react';
import Add_products from './add_products';

function Products() {
const navigate = useNavigate(); 

const handleNavigation = (page, path) => {
    setActivePage(page);
    navigate(path);
  };
  
const [activePage, setActivePage] = useState('PRODUCTS');
const [isModalOpen, setIsModalOpen] = useState(false);

  const products = [
    {
      id: 1,
      name: 'OKOK SUCRE',
      price: '1000f',
      rating: 4,
      image: 'https://recettesdafrique.com/wp-content/uploads/cooked/images/recipes/recipe_5005.jpg'
    },
    {
      id: 2,
      name: 'POULET BRAISE',
      price: '1000f',
      rating: 5,
      image: 'https://images.unsplash.com/photo-1700135925872-1df223756392?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxncmlsbGVkJTIwbWVhdCUyMGFmcmljYW58ZW58MXx8fHwxNzYxMDUwMzYxfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral'
    },
    {
      id: 3,
      name: 'POULET PANE',
      price: '1000f',
      rating: 4,
      image: 'https://images.unsplash.com/photo-1672856399624-61b47d70d339?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxmcmllZCUyMGNoaWNrZW4lMjBjcmlzcHl8ZW58MXx8fHwxNzYxMDIzMjQxfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral'
    },
    {
      id: 4,
      name: 'ERU',
      price: '1500f',
      rating: 5,
      image: 'data:image/jpeg;base64,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',
    },
    {
      id: 5,
      name: 'NDOLE',
      price: '1200f',
      rating: 4,
      image: 'data:image/jpeg;base64,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'
    },
    {
      id: 6,
      name: 'JOLLOF RICE',
      price: '800f',
      rating: 5,
      image: 'https://images.unsplash.com/photo-1653981608672-aea09b857b20?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxhZnJpY2FuJTIwcmljZSUyMGRpc2h8ZW58MXx8fHwxNzYxMDUwMzY1fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral'
    }
  ];


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

    
      <div className="products-content">
        {/* Header */}
        <div className="products-header">
          <div className="header-left">
            <h1>Zeduc-Sp@ce is open</h1>
            <p className="header-date"> Octobre 2025</p>
          </div>
          <div className="user-info">
            <span>Admin1_resto</span>
            <div className="user-avatar">A</div>
            <ChevronDown className="chevron-down" size={20} />
            <Bell className="notification-bell" size={20} />
          </div>
        </div>

        {/* Main Content */}
        <div className="products-main-content">
          <div className="title-row">
            <h2>Menu du jour</h2>
            <button 
              className="add-button"
              onClick={() => navigate('/add_products')}
            > 
              <Plus size={16} />
              Ajouter un plat
            </button>
          </div>

          {/* Products Grid */}
          <div className="products-grid">
            {products.map((product) => (
              <div key={product.id} className="product-card">
                <div className="product-image-wrapper">
                  <ImageWithFallback
                    src={product.image}
                    alt={product.name}
                    className="product-image"
                  />
                </div>
                <div className="product-info">
                  <h3 className="product-name">{product.name}</h3>
                  <p className="product-price">{product.price}</p>
                  <div className="product-rating">
                    {[1, 2, 3, 4, 5].map((star) => (
                      <Star
                        key={star}
                        size={16}
                        fill={star <= product.rating ? '#D4A574' : 'none'}
                        color={star <= product.rating ? '#D4A574' : '#666'}
                      />
                    ))}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </>
  );
}

export default Products;
