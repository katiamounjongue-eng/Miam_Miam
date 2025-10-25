import React from 'react';
import { useNavigate } from "react-router-dom";
import '../styles/accueil_admin.css'
import logo from '../assets/images/logo-zeduc.png'

function AccueilAdmin() {

    const navigate = useNavigate();

  const handleLogin = () => {
    // Tu peux mettre ici une vérification des identifiants si tu veux
    navigate("/admin"); // redirige vers la page AdminHome
  };

  return (
   <>

    <div class="login-container">
        <div class="logo-container">
            <img src={logo} alt="Zeduc Space Logo" class="logo"/>
        </div>

        <form class="login-form" onsubmit="handleSubmit(event)"/>
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Admin1_resto"
                    required
                />
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="•••••••••"
                    required
                />
            </div>

            <button type="submit" class="submit-btn" onClick={handleLogin}>Sign In</button> 
    </div>

    <script src="script.js"></script>

   </>
  );
}

export default AccueilAdmin;