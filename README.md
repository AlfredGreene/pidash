# pidash
---

A dashboard for the Raspberry Pi


## Installation: 
- Install a webserver like `nginx` from their official repositories
- Install `php5-fpm` and configure it to work with your webserver
- Download this repository to your webservers base directory
- Run the `setup.sh` in the root-directory of the repository as your webserver-user (e.g. *www-data*)
- Open up your webbrowser and navigate to the Pi's webserver you've just set up
- Half optional: Allow www-data to `sudo shutdown`
- Optional: modify the `config.php` in the root-directory

## Features
- Dashboard with system relevant information
- An OMXPlayer remote control
- A Jukebox for listening to your private music library on the Pi
- A HTTP-downloadserver
- A basic filebrowser
- A customizable login with a wait function for wrong passwords
- If it's installed: OpenVPN shortcut
- Responsive UI

## Howto: 
**Add a custom library to the player**

- Navigate to the pidash-directory
- Navigate to `pages/player/libs`
- Copy or modify the `example.php` to suit your needs
    
**Watch a nsfw library in the player**

- Open the player in your browser
- Append `?nsfw` to the URL

## Screenshots
**Login screen**
![Login screen](http://i.imgur.com/zUBxo6l.png)

**Dashboard**

On Desktop

![Dashboard desktop](http://i.imgur.com/D5oT87T.png) 

On Mobile

![Dashboard mobile](http://i.imgur.com/RRZACu4.png)

**Player**

Searching a movie

![Player mediathek](http://i.imgur.com/Gi7waHd.png)

When the movie is playing

![Player playing](http://i.imgur.com/wozvYHc.png)

**Jukebox**

![Jukebox](http://i.imgur.com/kIGhBxu.png)

**Downloader**

![Downloader](http://i.imgur.com/aHTIIRK.png)

**Filebrowser**

![Filebrowser](http://i.imgur.com/DzE8xsK.png)
