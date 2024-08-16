lhcheck = document.location.href.includes("127.0.0.1");
console.log("Localhost check: " + lhcheck);

if (document.body.contains(document.getElementById('strt-nw'))) {
  const chat = document.getElementById('chat');
  const startBtn = document.getElementById('strt-nw');
  startBtn.addEventListener('click', () => {
    if (lhcheck) {
      window.location.href = 'work-in-progress.html';
    } else {
      window.location.href = 'work-in-progress';
    }
  });
  chat.addEventListener('click', () => {
    if (lhcheck) {
      window.location.href = '/chat/index.php';
    } else {
      window.location.href = '/chat';
    }
  });
}

if (document.body.contains(document.getElementById('wip'))) {
  if (lhcheck) {
    window.location.href = 'work-in-progress.html';
  } else {
    window.location.href = 'work-in-progress';
  }
}

if (document.body.contains(document.getElementById('logo'))) {
  const home = document.getElementById('home');
  const about = document.getElementById('about');
  const blog = document.getElementById('blog');
  const services = document.getElementById('services');

  home.addEventListener('click', () => {
    if (lhcheck) {
      window.location.href = '/index.html';
    } else {
      window.location.href = 'https://silverflag.net';
    }
  });
  about.addEventListener('click', () => {
    if (lhcheck) {
      window.location.href = '/about.html';
    } else {
      window.location.href = '/about';
    }
  });
  blog.addEventListener('click', () => {
    if (lhcheck) {
      window.location.href = '/blog.html';
    } else {
      window.location.href = '/blog';
    }
  });
  services.addEventListener('click', () => {
    if (lhcheck) {
      window.location.href = '/services.html';
    } else {
      window.location.href = '/services';
    }
  });
}