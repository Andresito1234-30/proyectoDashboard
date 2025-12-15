// utils/jwt.js
const jwt = require("jsonwebtoken");
const { JWT_SECRET_KEY } = require("../constante");

function createAccessToken(user) {
  const expToken = new Date();
  expToken.setMinutes(expToken.getMinutes() + 1); // 15 minutos

  const payload = {
    token_type: "access",
    user_id: user._id,
    iat: Math.floor(Date.now() / 1000),
    exp: Math.floor(expToken.getTime() / 1000),
  };

  return jwt.sign(payload, JWT_SECRET_KEY);
}

function createRefreshToken(user) {
  const expToken = new Date();
  expToken.setMonth(expToken.getMonth() + 1);

  const payload = {
    token_type: "refresh",
    user_id: user._id,
    iat: Math.floor(Date.now() / 1000),
    exp: Math.floor(expToken.getTime() / 1000),
  };

  return jwt.sign(payload, JWT_SECRET_KEY);
}

function decoded(token) {
  return jwt.decode(token, JWT_SECRET_KEY, true);
}

function verify(token) {
  return jwt.verify(token, JWT_SECRET_KEY);
}

module.exports = {
  createAccessToken,
  createRefreshToken,
  decoded,
  verify,
};