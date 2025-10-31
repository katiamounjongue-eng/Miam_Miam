// src/pages/Statistics.jsx
import React from "react";
import "./Statistics.css";

const stats = [
  { title: "Revenus actuels", value: "", subtitle: "34 550F" },
  { title: "Commandes Acceptées", value: "41/42", subtitle: "97%" },
  { title: "Heures d'ouverture :", value: "", subtitle: "8AM - 8PM" },
];

function TopSelling() {
  const items = [
    "Pilé pommes",
    "Beef Medium Que",
    "Lasagna Emmiratee",
    "Chicken Mozarella",
    "Chicken Cordenblu",
    "Burger + Mash Potato",
    "Beef Teriyaki",
    "Spicy Bulgogi",
    "Pork Quesadilla",
    "Pickled Chili's",
  ];

  return (
    <div className="card top-selling" aria-label="Top selling items">
      <h3 className="card-title">Top-selling</h3>
      <ol className="top-list">
        {items.map((it, idx) => (
          <li key={it}>
            <span className="rank">{idx + 1}</span>
            <span className="item">{it}</span>
          </li>
        ))}
      </ol>
    </div>
  );
}

function PresentationChart() {
  return (
    <div className="card presentation" aria-label="Graphique de présentation">
      <div className="card-header">
        <h1 className="card-title">Présentation</h1>
        <select className="small-select" aria-label="Période">
          <option>Week</option>
          <option>Month</option>
        </select>
      </div>

      {/* placeholder pour le chart — ajoute ici le JSX du graphique si tu veux */}
    </div>
  );
}

export default function Statistics() {
  return (
    <div className="dashboard-root">
      <div className="dashboard">
        <div className="top-row">
          {stats.map((s) => (
            <div className="stat-card card" key={s.title}>
              <div className="stat-title">{s.title}</div>
              <div className="stat-value">{s.value}</div>
              {s.subtitle && <div className="stat-sub">{s.subtitle}</div>}
            </div>
          ))}
        </div>

        <div className="main-grid">
          <PresentationChart />
          <TopSelling />
        </div>
      </div>
    </div>
  );
}
