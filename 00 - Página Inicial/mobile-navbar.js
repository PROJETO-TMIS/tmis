class MobileNavbar {
  constructor(mobileMenu, navList, navLinks) {
    this.mobileMenu = document.querySelector(mobileMenu);
    this.navList = document.querySelector(navList);
    this.navLinks = document.querySelectorAll(navLinks);
    this.activeClass = "active";

    this.handleClick = this.handleClick.bind(this);
  }

  animateLinks() {
    this.navLinks.forEach((link, index) => {
      if (link.style.animation) {
        link.style.animation = "";
      } else {
        link.style.animation = `navLinkFade 0.5s ease forwards 0.2s`;
      }
    });
  }

  handleClick() {
    console.log(this);
    this.navList.classList.toggle(this.activeClass);
    this.animateLinks(); // agora a animação é chamada
  }

  addClickEvent() {
    this.mobileMenu.addEventListener("click", this.handleClick);
  }

  init() {
    if (this.mobileMenu) {
      this.addClickEvent();
    }
    return this;
  }
}

const mobileNavbar = new MobileNavbar(
  ".mobile-menu",
  ".nav-list",
  ".nav-list li"
);

mobileNavbar.init();

function abrirModal() {
  document.getElementById("modalVideo").style.display = "flex";
  const video = document.getElementById("videoAmpliado");
  video.currentTime = 0;
  video.play();
}

function fecharModal() {
  const modal = document.getElementById("modalVideo");
  const video = document.getElementById("videoAmpliado");
  video.pause();
  modal.style.display = "none";

}

