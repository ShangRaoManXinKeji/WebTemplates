require('dotenv').config();
const TelegramBot = require('node-telegram-bot-api');
const handleMessage = require('./handlers/messageHandler');

const bot = new TelegramBot(process.env.TELEGRAM_TOKEN, { polling: true });

bot.on('message', (msg) => {
  handleMessage(bot, msg);
});
