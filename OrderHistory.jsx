// src/pages/OrdersHistory.jsx
import React from "react";
import "./Orders History.css"; // adapte le nom si ton fichier s'appelle autrement (ex: "./Orders History.css")

const orders = Array.from({ length: 10 }).map((_, i) => ({
    id: `#21${5 + i}`,
    customer: "Jeremmy Passion",
    menu: "Loin of Vension (2)",
    price: "$ 12.5",
    date: "22 Mai 2021, 12:21 PM",
    status:
        i % 5 === 0
            ? "TERMINÉ"
            : i % 5 === 1
                ? "REJECTED"
                : i % 5 === 2
                    ? "PREPARATION"
                    : i % 5 === 3
                        ? "EN ROUTE"
                        : "COMPLETED",
}));

function StatusPill({ status }) {
    // garde le même format de classe que ton CSS (attention aux accents si tu veux éviter)
    const className = `status-pill status-${status.replace(/\s+/g, "-").toLowerCase()}`;
    return <span className={className}>{status}</span>;
}

function OrderRow({ order, index }) {
    return (
        <div className="order-row" key={`${order.id}-${index}`}>
            <div className="col customer">
                <span className="avatar" aria-hidden />
                <div className="customer-info">
                    <div className="customer-name">{order.customer}</div>
                </div>
            </div>

            <div className="col id">{order.id}</div>

            <div className="col menu">
                <div className="menu-title">{order.menu}</div>
                <div className="menu-price">{order.price}</div>
            </div>

            <div className="col date">{order.date}</div>

            <div className="col status">
                <StatusPill status={order.status} />
            </div>
        </div>
    );
}

export default function OrdersHistory() {
    return (
        <div className="orders-root">
            <div className="orders-container card">
                <header className="orders-header">
                    <div className="left">
                        <div className="search">
                            <input placeholder="Search" aria-label="Search orders" />
                            <button className="search-btn" aria-label="Search">
                                🔍
                            </button>
                        </div>
                        <h2 className="title">Historique des commandes</h2>
                    </div>

                    <div className="right">
                        <button className="filter-btn">Filter Order</button>
                    </div>
                </header>

                <div className="table-headers">
                    <div className="col customer">Customer</div>
                    <div className="col id">ID</div>
                    <div className="col menu">Menu</div>
                    <div className="col date">Date</div>
                    <div className="col status">Status</div>
                </div>

                <div className="orders-list">
                    {orders.map((o, idx) => (
                        <React.Fragment key={`${o.id}-${idx}`}>
                            <OrderRow order={o} index={idx} />
                            <div className="divider" />
                        </React.Fragment>
                    ))}
                </div>
            </div>
        </div>
    );
}
