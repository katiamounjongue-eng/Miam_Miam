import React, { useState } from 'react';
import '../styles/messages_admin.css';
import logo from '../assets/images/logo-zeduc.png'
import { House, AlarmClockCheck, MessagesSquare, TrendingUp, UtensilsCrossed, Settings, UserCog, Search, Paperclip, Send } from 'lucide-react';

function MessagesAdmin() {
  const [activePage, setActivePage] = useState('MESSAGES');
  const [activeConversation, setActiveConversation] = useState('Jeremmy Passion #215');

  const conversations = [
    { id: 1, initial: 'S', name: 'Support Center', preview: 'You can change your password in th..', color: '#474747' },
    { id: 2, initial: 'S', name: 'Security', preview: "Your OTP is 3221.  Don't Share this ..", color: '#494FDD' },
    { id: 3, initial: 'J', name: 'Jeremmy Passion #215', preview: 'Hello sir, I wanna take away some L..', color: '#BE2828', active: true },
    { id: 4, initial: 'K', name: 'Kathleen #219', preview: 'Hello sir, I wanna take away some L..', color: '#A54651' },
    { id: 5, initial: 'P', name: 'Peter Parker #220', preview: 'Hello sir, I wanna take away some L..', color: '#BD8414' },
    { id: 6, initial: 'C', name: 'Chester Milan #219', preview: 'Hello sir, I wanna take away some L..', color: '#469FA5' },
    { id: 7, initial: 'M', name: 'Mark #223', preview: 'Hello sir, I wanna take away some L..', color: '#87BD14' },
    { id: 8, initial: 'B', name: 'Bob Manuel #227', preview: 'Hello sir, I wanna take away some L..', color: '#465BA5' },
    { id: 9, initial: 'Z', name: 'Zavier Zanetti #228', preview: 'Hello sir, I wanna take away some L..', color: '#726D63' }
  ];

  const messages = [
    { id: 1, text: 'Hi, can I get this Loin of Vension to take out?', time: '12.21 PM', sender: 'customer' },
    { id: 2, text: 'Sure, with my pleasure sir. Wait a moment', time: '12.21 PM', sender: 'admin' },
    { id: 3, text: 'Okay Thankyou!', time: '12.21 PM', sender: 'customer' },
    { id: 4, text: 'Your order is completed. Hope you enjoy the food :)', time: '12.21 PM', sender: 'admin' }
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


      <div className="messages-content">
        {/* Conversations List */}
        <div className="conversations-panel">
          <h1 className="messages-title">MESSAGES</h1>
          
          <div className="search-wrapper">
            <input type="text" placeholder="Search"/>
            <Search className="search-icon" size={18} />
          </div>

          <div className="conversations-list">
            {conversations.map((conv) => (
              <div 
                key={conv.id} 
                className={`conversation-item ${conv.active ? 'active' : ''}`}
                onClick={() => setActiveConversation(conv.name)}
              >
                <div 
                  className="conversation-avatar" 
                  style={{ backgroundColor: conv.color }}
                >
                  {conv.initial}
                </div>
                <div className="conversation-info">
                  <div className="conversation-name">{conv.name}</div>
                  <div className="conversation-preview">{conv.preview}</div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Chat Panel */}
        <div className="chat-panel">
          {/* Chat Header */}
          <div className="chat-header">
            <div className="chat-avatar">J</div>
            <div className="chat-user-info">
              <div className="chat-user-name">Jeremmy Passion #215</div>
              <div className="chat-user-status">
                <span className="status-indicator"></span>
                Online
              </div>
            </div>
          </div>

          <div className="chat-divider"></div>

          {/* Chat Messages */}
          <div className="chat-messages">
            {messages.map((message) => (
              <div 
                key={message.id} 
                className={`message ${message.sender === 'customer' ? 'message-customer' : 'message-admin'}`}
              >
                <div className="message-bubble">
                  {message.text}
                </div>
                <div className="message-time">{message.time}</div>
              </div>
            ))}
          </div>

          {/* Chat Input */}
          <div className="chat-input-container">
            <input 
              type="text" 
              className="chat-input" 
              placeholder="Type something"
            />
            <div className="input-divider"></div>
            <button className="attach-button">
              <Paperclip size={18} color="#EFC581" />
            </button>
            <button className="send-button">
              <Send size={18} color="#EFC581" />
            </button>
          </div>
        </div>
      </div>
    </>
  );
}

export default MessagesAdmin;
