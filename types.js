// src/types.js
import PropTypes from 'prop-types';

/**
 * @typedef {Object} Account
 * @property {number} id
 * @property {string} name
 * @property {string} email
 * @property {string} createdAt
 * @property {string} role
 */

/**
 * PropType réutilisable pour les components React en JS
 * Exemple d'utilisation: MyComponent.propTypes = { account: AccountPropType.isRequired }
 */
export const AccountPropType = PropTypes.shape({
  id: PropTypes.number.isRequired,
  name: PropTypes.string.isRequired,
  email: PropTypes.string.isRequired,
  createdAt: PropTypes.string.isRequired,
  role: PropTypes.string.isRequired,
});
