import React, { useState } from 'react';
import { ArrowLeft, ImagePlus } from 'lucide-react';
import '../styles/add_products.css';

function Add_products({ isOpen, onClose }) {
  const [dishName, setDishName] = useState('');
  const [description, setDescription] = useState('');
  const [price, setPrice] = useState('');
  const [image, setImage] = useState(null);
  const [imagePreview, setImagePreview] = useState(null);

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setImage(file);
      const reader = new FileReader();
      reader.onloadend = () => {
        setImagePreview(reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Logique de soumission ici
    console.log({ dishName, description, price, image });
    // Réinitialiser le formulaire
    setDishName('');
    setDescription('');
    setPrice('');
    setImage(null);
    setImagePreview(null);
    onClose();
  };

  if (!isOpen) return null;

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-container" onClick={(e) => e.stopPropagation()}>
        <button className="back-button" onClick={onClose}>
          <ArrowLeft size={24} />
        </button>

        <form className="modal-form" onSubmit={handleSubmit}>
          <div className="form-group">
            <label className="form-label">Nom du consommable</label>
            <input
              type="text"
              className="form-input"
              placeholder="Entrez le nom du plat"
              value={dishName}
              onChange={(e) => setDishName(e.target.value)}
              required
            />
          </div>

          <div className="form-group">
            <label className="form-label">Description</label>
            <input
              type="text"
              className="form-input"
              placeholder="Entrez une courte description du plat"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              required
            />
          </div>

          <div className="form-group">
            <label className="form-label">Prix</label>
            <input
              type="text"
              className="form-input"
              placeholder="Entrez le prix du plat"
              value={price}
              onChange={(e) => setPrice(e.target.value)}
              required
            />
          </div>

          <div className="image-upload-container">
            <input
              type="file"
              id="image-upload"
              className="image-upload-input"
              accept="image/*"
              onChange={handleImageChange}
            />
            <label htmlFor="image-upload" className="image-upload-label">
              {imagePreview ? (
                <img src={imagePreview} alt="Preview" className="image-preview" />
              ) : (
                <div className="image-upload-placeholder">
                  <ImagePlus size={32} color="#ffa500" />
                  <span className="upload-text">déposez une image du plat</span>
                </div>
              )}
            </label>
          </div>

          <button type="submit" className="validate-button">
            Valider
          </button>
        </form>
      </div>
    </div>
  );
}

export default Add_products;
