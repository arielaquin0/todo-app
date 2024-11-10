FROM node:20

WORKDIR /var/www/frontend

COPY frontend/package*.json ./

RUN npm install

COPY frontend .

EXPOSE 8000

CMD ["npm", "run", "dev"]
