$(document).ready(function () {
  
  var app = $.spapp({
    defaultView: "home",
    templateDir: "./"
  });

  app.route({
    view: "home",
    load: "views/home.html",
    onCreate: function() {
      console.log('Home created!');
    },
    onReady: function() {
      console.log('Home ready!');
    }
  });

  app.route({
    view: "teams",
    load: "views/teams.html",
    onCreate: function() {
      console.log('Teams created!');
    },
    onReady: function() {
      console.log('Teams ready!');
    }
  });

  app.route({
    view: "players",
    load: "views/players.html",
    onCreate: function() {
      console.log('Players created!');
    },
    onReady: function() {
      console.log('Players ready!');
    }
  });

  app.route({
    view: "matches",
    load: "views/matches.html",
    onCreate: function() {
      console.log('Matches created!');
    },
    onReady: function() {
      console.log('Matches ready!');
    }
  });

  app.route({
    view: "venues",
    load: "views/venues.html",
    onCreate: function() {
      console.log('Venues created!');
    },
    onReady: function() {
      console.log('Venues ready!');
    }
  });

  app.route({
    view: "login",
    load: "views/login.html",
    onCreate: function() {
      console.log('Login created!');
    },
    onReady: function() {
      console.log('Login ready!');
    }
  });

  app.route({
    view: "register",
    load: "views/register.html",
    onCreate: function() {
      console.log('Register created!');
    },
    onReady: function() {
      console.log('Register ready!');
    }
  });

  
  app.route({
    view: "standings",
    load: "views/standings.html",
    onCreate: function() {
      console.log('Standings created!');
    },
    onReady: function() {
      console.log('Standings ready!');
    }
  });

  app.run();

});