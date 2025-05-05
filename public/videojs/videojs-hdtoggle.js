/*
    HD/SD Toggle Button for VideoJS
    Version 0.3
    compatible with VideoJS 3.2
    Author: Felix Schwarz
    
    "HD/SD Toggle Button for VideoJS" is free software: 
    you can redistribute it and/or modify it under the terms of the 
    GNU Lesser General Public License as published by the Free Software 
    Foundation, either version 3 of the License, or (at your option) any later 
    version.
    
    The software is distributed in the hope that it will be useful, but WITHOUT 
    ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
    FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public 
    License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with VideoJS.  If not, see <http://www.gnu.org/licenses/>.
*/

VideoJS.HDToggleButton = VideoJS.Component.extend({
    /* --- initialization --------------------------------------------------- */
    
    init: function(player, options){
        this.sdURL = null;
        this.hdURL = options.hdURL || player.tag.getAttribute('data-hdUrl') || null;
        this.isUsingSD = true;
        this._isPlaying = false;
        this.addListeners(player);
        // this._super will call 'createElement' immediately, therefore we need
        // to create all instance variables before
        this._super(player, options);
    },
    
    addListeners: function(player) {
        player.addEvent('play', this.proxy(this.onVideoPlay));
        player.addEvent('pause', this.proxy(this.onVideoPaused));
        player.addEvent('ended', this.proxy(this.onVideoEnded));
        player.addEvent('error', this.proxy(this.onVideoError));
    },
    
    createElement: function() {
        if (this.hdURL === null)
            return this._super("div", {});
        var hdButton = this._super("div", {
            className: 'vjs-hd-switcher vjs-sd vjs-menu-button vjs-control',
            innerHTML: '<span>HD</span>'
        });
        hdButton.addEventListener("click", this.proxy(function(event) { this.onHDButtonClick(event, hdButton); }), false);  
        return hdButton;
    },
    
    /* --- accessing the "model" -------------------------------------------- */
    
    isPlaying: function() {
        var video = this.videoTag();
        if (this._isPlaying && video && (video.ended || video.paused)) {
            VideoJS.log('inconsistent state: video/HTML5 says ended or paused but as far as we know it is still playing...');
            this._isPlaying = false;
        }
        return !! this._isPlaying;
    },
    
    videoTag: function() {
        // this circumvents VideoJS's API and assumes HTML5 henceforth!
        return this.player.tag;
    },
    
    currentSource: function() {
        var video = this.videoTag();
        return video.src || video.currentSrc;
    },
    
    restartPlaying: function() {
        this.player.play();
    },
    
    /* --- event handling --------------------------------------------------- */
    onVideoPlay: function(event) {
        this._isPlaying = true;
    },
    
    onVideoPaused: function(event) {
        this._isPlaying = false;
    },
    
    onVideoEnded: function(event) {
        this._isPlaying = false;
    },
    
    onVideoError: function(event) {
        this._isPlaying = false;
    },

    onHDButtonClick: function(event, button) {
        currentTime = this.player.currentTime();
        
        // after we replaced the source, the player will be paused (HTML5/Chrome 19/Linux)
        // to accurately restore the playing state, we need to query the state before
        // replacing the source
        wasPlaying = this.isPlaying();
        
        if (this.isUsingSD) {
            this.sdURL = this.currentSource();
            this.player.src(this.hdURL);
            VideoJS.removeClass(button, "vjs-sd");
            VideoJS.addClass(button, "vjs-hd");
        } else {
            this.player.src(this.sdURL);
            VideoJS.removeClass(button, "vjs-hd");
            VideoJS.addClass(button, "vjs-sd");
        }
        //if we want to support preservation of current time we need to wait 
        // until video is "ready"
        //this.player.currentTime(currentTime);
        this.isUsingSD = (! this.isUsingSD);
        
        if (wasPlaying)
            // DOM needs to have a chance to update, otherwise the 'play' command
            // to VideoJS won't be honored (HTML5/Chrome 19/Linux)
            window.setTimeout(this.proxy(this.restartPlaying), 0);
    }
});


VideoJS.merge(VideoJS.ControlBar.prototype.options.components, {
    "HDToggleButton": {}
});

