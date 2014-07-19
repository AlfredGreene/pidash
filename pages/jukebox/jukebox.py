#!/usr/bin/python
import sys
import os
import os.path
import pygame
import time
import tty
import termios
import thread
import random
import subprocess
import json

class Jukebox(object): 
    def __init__(self): 
        os.system("sudo amixer set PCM 100% > /dev/null")
        pygame.mixer.init()
        pygame.display.set_mode((0, 0))
        pygame.mixer.music.set_endevent(pygame.USEREVENT)
        pygame.display.set_caption("Jukebox")
        pygame.display.iconify()
        random.seed()
        
        self.info = {}
        
        self.active = 0
        self.paused = False
        
        self.time = 0
        self._gettime = True
        self._resettime = False
        
        self.load_playlist()
    
    def load_playlist(self, set_active=True): 
        self.playlist = []
        cached = self.info.keys()
        
        if os.path.exists("playlist.txt"): 
            with open("playlist.txt", "r") as f: 
                z = 0
                for i in f.read().strip().split("\n"): 
                    k = i.split(" ")
                    if len(k) > 1 and len(k[0]) == 6: 
                        if set_active: 
                            self.active = z
                        i = " ".join(k[1:])
                        self.playlist.append(i)
                    else: 
                        self.playlist.append(i)
                    
                    if i not in cached: 
                        self.info[i] = {"title": "", "artist": "", "album": "", "empty": "true"}
                        thread.start_new_thread(self.cacheAudioInfo, tuple([i]))
                    
                    z += 1
    
    def dump_playlist(self): 
        k = ""
        for t in range(0, len(self.playlist)): 
            if t == self.active: 
                k += "active " + self.playlist[t]
            else: 
                k += self.playlist[t]
            k += "\n"
        
        with open("playlist.txt", "w") as f: 
            f.write(k)
    
    def play(self, file): 
        if len(self.playlist) == 1 or self.playlist[0].strip() == "": 
            self.active = 0
            return
        
        if not self.playlist.__contains__(file): 
            self.playlist.append(file)
        
        try: 
            pygame.mixer.music.load(file)
            pygame.mixer.music.set_volume(1.0)
            pygame.mixer.music.play()
            self.paused = False
            self._resettime = True
        except: 
            self.next(False)
    
    def pause(self): 
        if self.paused: 
            pygame.mixer.music.unpause()
        else: 
            pygame.mixer.music.pause()
        
        self.paused = not self.paused
    
    def next(self, set_active=True): 
        self.load_playlist(set_active)
        
        self.active += 1
        if self.active > len(self.playlist) - 1: 
            self.active = 0
        
        self.play(self.playlist[self.active])
        self.dump_playlist()
    
    def prev(self): 
        self.load_playlist()
        
        self.active -= 1
        if self.active < 0: 
            self.active = len(self.playlist) - 1
        
        self.play(self.playlist[self.active])
        self.dump_playlist()
    
    def read(self): 
        fd = sys.stdin.fileno()
        s = termios.tcgetattr(fd)
        c = None
        try: 
            tty.setraw(sys.stdin.fileno())
            c = sys.stdin.read(1)
        finally: 
            termios.tcsetattr(fd, termios.TCSADRAIN, s)
        return c
    
    def _time(self): 
        interval = 0.5
        while self._gettime: 
            if self._resettime: 
                self._resettime = False
                self.time = 0
            else: 
                self.time += interval
            
            for event in pygame.event.get(): 
                if event.type == pygame.USEREVENT: 
                    self.next()
            
            time.sleep(interval)
    
    def randomize(self): 
        p = [self.playlist[self.active]]
        t = len(self.playlist) - 1
        d = [0]
        for i in range(0, t): 
            z = random.randint(1, t)
            while d.__contains__(z): 
                random.seed()
                z = random.randint(1, t)
            p.append(self.playlist[z])
            d.append(z)
        self.active = 0
        self.playlist = p
        self.dump_playlist()
    
    def cacheAudioInfo(self, filename): 
        t = subprocess.Popen(["ffmpeg", "-i", filename], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        stdout, err = t.communicate()
        i = err.strip().split("\n")[7:-3]
        o = {"title": "", "artist": "", "album": "", "empty": "false"}
        j = o.keys()
        
        for k in i: 
            k = k.strip()
            n = k.split(":")[0].strip()
            if n in j: 
                o[n] = k[k.find(":") + 1:].strip()
        
        self.info[filename] = o

def log(t): 
    if len(sys.argv) == 2: 
        with open(sys.argv[1], "a") as f: 
            f.write(t + "\n")

if __name__ == "__main__": 
    j = Jukebox()
    
    thread.start_new_thread(j._time, ())
    
    while True: 
        i = j.read()
        if i == " ": 
            j.pause()
        elif i == "n": 
            j.next()
        elif i == "p": 
            j.prev()
        elif i == "r": 
            j.randomize()
        elif i == "i": 
            o = j.info[j.playlist[j.active]]
            o["time"] = int(j.time)
            o["paused"] = j.paused
            o = json.dumps(o).encode("base64").replace("\n", "")
            print o
            log(o)
        elif i == "q": 
            j._gettime = False
            break
    
    sys.exit(0)