import React from 'react';
import { NavLink } from 'react-router-dom';
import './Sidebar.css';
import logo from '../images/LogoZ.png';

const navItems = [
    {
        to: '/order-history',
        label: 'Order History',
        icon: (
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M3 2h14l4 4v16l-4-2-4 2-4-2-4 2V2zM7 8h8v2H7V8zm0 4h8v2H7v-2z" />
            </svg>
        ),
    },
    {
        to: '/messages',
        label: 'Messages',
        icon: (
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M20 2H4a2 2 0 0 0-2 2v14l4-2h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM6 9h12v2H6V9zm0-3h12v2H6V6z" />
            </svg>
        ),
    },
    {
        to: '/statistics',
        label: 'Statistics',
        icon: (
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M3 3v18h18v-2H5V3H3zm6 6h2v9H9V9zm4-4h2v13h-2V5zm4 7h2v6h-2v-6z" />
            </svg>
        ),
    },
    {
        to: '/accounts',
        label: 'Account Management',
        icon: (
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4 0-8 2-8 5v3h16v-3c0-3-4-5-8-5z" />
            </svg>
        ),
    },
];

function Sidebar() {
    return (
        <nav className="app-sidebar">
            <div className="sidebar-header">
                <img src={logo} alt="Logo" className="sidebar-logo" />
            </div>

            <ul className="nav flex-column sidebar-list">
                {navItems.map((item) => (
                    <li key={item.to} className="nav-item">
                        <NavLink
                            to={item.to}
                            end
                            className={({ isActive }) =>
                                `nav-link d-flex align-items-center ${isActive ? 'active' : ''}`
                            }
                        >
                            <span className="icon" aria-hidden="true">
                                {item.icon}
                            </span>
                            <span className="label">{item.label}</span>
                        </NavLink>
                    </li>
                ))}
            </ul>

            <div className="sidebar-footer" role="status" aria-live="polite">
                <span className="status-dot" aria-hidden="true" />
                <span className="status-text">restaurant open</span>
            </div>
        </nav>
    );
}

export default Sidebar;
