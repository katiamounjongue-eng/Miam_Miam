// Header.jsx
import React from "react";
import "./Header.css"; // on externalise son CSS

export default function Header() {
    return (
        <header className="app-header">
            <div className="header-left">
                <div className="header-title">
                    Zeduc-Sp@ce is open
                    <span className="status-dot" />
                </div>
                <div className="header-date">22 Mai 2021, 12:21 PM</div>
            </div>

            <div className="header-right">
                <div className="header-user">Admin1_resto</div>
                <div className="header-avatar">A</div>
                <button aria-label="notifications" className="header-btn">
                    <svg
                        className="header-icon"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            strokeWidth="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0"
                        />
                    </svg>
                </button>
            </div>
        </header>
    );
}
