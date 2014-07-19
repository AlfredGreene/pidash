pidash
======

A dashboard for the Raspberry Pi


Installation: 
- Install a webserver like nginx
- Install php5 and configure it to work with your webserver
- Download the repository to your webservers directory
- Run the `setup.sh` in the root-directory of the repository as your webserver-user (e.g. www-data)
- Open up your webbrowser and navigate to the Pi's webserver you've just set up
- Optional: modify the `config.php` in the root-directory
- Enjoy the dashboard


Howto: 
- add a custom library to the player: 
    - Navigate to the pidash-directory
    - Navigate to `pages/player/libs`
    - Copy or modify the `example.php` to suit your needs
    - Open the player in your browser, the library should be there now
- watch a nsfw library in the player: 
    - Open the player in your browser
    - Append `?nsfw` to the URL
