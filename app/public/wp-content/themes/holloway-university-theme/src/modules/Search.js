import $ from "jquery";

class Search {
  // 1. describe and intiate object
  constructor() {
    this.addSearchHtml();
    this.results = $("#search-overlay__results");
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchInput = $("#search-term");
    this.events();
    this.isOpen = false;
    this.spinnerVisible = false;
    this.prevValue;
    this.timer;
  }

  // 2. events
  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    $(document).on("keydown", this.keyPressDispatch.bind(this));
    this.searchInput.on("keyup", this.typingLogic.bind(this));
  }

  // 3. methods
  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    this.searchInput.val("");
    setTimeout(() => {
      this.searchInput[0].focus();
      this.searchInput[0].select();
    }, 301);
    this.isOpen = true;
  }
  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOpen = false;
  }

  keyPressDispatch(e) {
    if (e.keyCode == 83 && !this.isOpen && !$("input, textarea").is(":focus")) {
      this.openOverlay();
    }
    if (e.keyCode == 27 && this.isOpen) {
      this.closeOverlay();
    }
  }

  typingLogic() {
    if (this.searchInput.val() != this.prevValue) {
      clearTimeout(this.timer);

      if (this.searchInput.val()) {
        if (!this.spinnerVisible) {
          this.results.html('<div class="spinner-loader"></div>');
          this.spinnerVisible = true;
        }
        this.timer = setTimeout(this.getResults.bind(this), 750);
      } else {
        this.results.html("");
        this.spinnerVisible = false;
      }
    }
    this.prevValue = this.searchInput.val();
  }

  getResults() {
    // prettier-ignore
    $.when(
      $.getJSON(universityData.root_url + "/wp-json/wp/v2/posts?search=" + this.searchInput.val()), 
      $.getJSON(universityData.root_url + "/wp-json/wp/v2/pages?search=" + this.searchInput.val())
    ).then((posts, pages) => {
      let combinedResults = posts[0].concat(pages[0]);
      this.results.html(`
          <h2 class="search-overlay__section-title">Search Results</h2>
          ${
            combinedResults.length
              ? '<ul class="link-list min-list">'
              : "<p>No general information matches that search.</p>"
          }
            ${combinedResults.map(
                (item) =>
                  `<li><a href="${item.link}">${item.title.rendered}</a></li>`
              )
              .join("")}
          ${combinedResults.length ? "</ul>" : ""}
        `);
      this.spinnerVisible = false;
    }, () => {
      this.results.html('<p>Unexpected error. Please try again.</p>')
    });
  }

  addSearchHtml() {
    $("body").append(`
        <div class="search-overlay">
            <div class="search-overlay__top">
                <div class="container">
                    <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                    <input class="search-term" placeholder="What are you looking for?" type="text" id="search-term">
                    <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                </div>
            </div>

            <div class="container">
                <div id="search-overlay__results">
                    
                </div>
            </div>
        </div>
      `);
  }
}

export default Search;
