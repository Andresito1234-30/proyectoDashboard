const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const {API_VERSION} = require('./constante');
const app = express();

app.use(bodyParser.urlencoded({extended: false}));
app.use(bodyParser.json());

app.use(express.static(`uploads`));

app.use(cors());
 
const authRoutes = require('./router/auth');
app.use(`/api/${API_VERSION}`, authRoutes);


module.exports = app;

