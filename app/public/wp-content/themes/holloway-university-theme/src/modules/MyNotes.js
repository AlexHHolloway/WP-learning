import axios from "axios";

class MyNotes {
  constructor() {
    if (document.querySelector("#my-notes")) {
      axios.defaults.headers.common["X-WP-Nonce"] = universityData.nonce;
      this.events();
      this.checkNoteLimit();
    }
  }
  events() {
    document.querySelector("#my-notes").addEventListener("click", (e) => {
      if (e.target.closest(".delete-note")) {
        this.deleteNote(e);
      }
      if (e.target.closest(".edit-note")) {
        this.editNote(e);
      }
      if (e.target.closest(".update-note")) {
        this.updateNote(e);
      }
    });
    document
      .querySelector(".submit-note")
      .addEventListener("click", this.createNote.bind(this));
  }

  // Check note limit on page load
  checkNoteLimit() {
    var noteCount = document.querySelectorAll("#my-notes li").length;
    if (noteCount >= 5) {
      document.querySelector(".note-limit-message").classList.add("active");
    }
  }

  // custom methods
  async createNote(e) {
    var newPost = {
      title: document.querySelector(".new-note-title").value,
      content: document.querySelector(".new-note-body").value,
      status: "private",
    };

    try {
      const response = await axios.post(
        universityData.root_url + "/wp-json/wp/v2/note",
        newPost
      );

      document.querySelector(".new-note-title").value = "";
      document.querySelector(".new-note-body").value = "";

      const newNote = document.createElement("li");
      newNote.setAttribute("data-id", response.data.id);
      newNote.innerHTML = `
        <input readonly class="note-title-field" value="${response.data.title.raw}" type="text">
        <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
        <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
        <textarea readonly class="note-body-field">${response.data.content.raw}</textarea>
        <span class="update-note btn btn--blue btn--small">Save <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
      `;

      document.querySelector("#my-notes").prepend(newNote);

      // Show limit message if user has reached 5 notes
      if (response.data.noteCount >= 5) {
        document.querySelector(".note-limit-message").classList.add("active");
      }

      console.log("Creation Successful.");
      console.log(response.data);
    } catch (error) {
      if (
        error.response &&
        error.response.data &&
        error.response.data.message
      ) {
        alert(error.response.data.message);
        console.log(error.response);
      } else {
        console.log("ERROR!");
        console.log(error);
        alert("Error creating note. Please try again.");
      }
    }
  }

  async updateNote(e) {
    var thisNote = e.target.closest("li");
    var updatedPost = {
      title: thisNote.querySelector(".note-title-field").value,
      content: thisNote.querySelector(".note-body-field").value,
    };

    try {
      const response = await axios.post(
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.dataset.id,
        updatedPost
      );

      this.makeNoteReadOnly(thisNote);
      console.log("Update Successful.");
      console.log(response.data);
    } catch (error) {
      console.log("ERROR!");
      console.log(error);
    }
  }

  editNote(e) {
    var thisNote = e.target.closest("li");
    if (thisNote.dataset.state == "editable") {
      this.makeNoteReadOnly(thisNote);
    } else {
      this.makeNoteEditable(thisNote);
    }
  }

  makeNoteEditable(thisNote) {
    thisNote.querySelector(
      ".edit-note"
    ).innerHTML = `<i class="fa fa-times" aria-hidden=true></i> Cancel`;
    thisNote.querySelector(".note-title-field").removeAttribute("readonly");
    thisNote
      .querySelector(".note-title-field")
      .classList.add("note-active-field");
    thisNote.querySelector(".note-body-field").removeAttribute("readonly");
    thisNote
      .querySelector(".note-body-field")
      .classList.add("note-active-field");
    thisNote
      .querySelector(".update-note")
      .classList.add("update-note--visible");
    thisNote.dataset.state = "editable";
  }

  makeNoteReadOnly(thisNote) {
    thisNote.querySelector(
      ".edit-note"
    ).innerHTML = `<i class="fa fa-pencil" aria-hidden=true></i> Edit`;
    thisNote
      .querySelector(".note-title-field")
      .setAttribute("readonly", "readonly");
    thisNote
      .querySelector(".note-title-field")
      .classList.remove("note-active-field");
    thisNote
      .querySelector(".note-body-field")
      .setAttribute("readonly", "readonly");
    thisNote
      .querySelector(".note-body-field")
      .classList.remove("note-active-field");
    thisNote
      .querySelector(".update-note")
      .classList.remove("update-note--visible");
    thisNote.dataset.state = "cancel";
  }

  async deleteNote(e) {
    var thisNote = e.target.closest("li");

    try {
      const response = await axios.delete(
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.dataset.id
      );

      // Slide up animation
      thisNote.classList.add("fade-out");

      setTimeout(() => {
        thisNote.remove();
        var remainingNotes = document.querySelectorAll("#my-notes li").length;
        if (remainingNotes < 5) {
          document
            .querySelector(".note-limit-message")
            .classList.remove("active");
        }
      }, 330);

      console.log("Removal Successful.");
      console.log(response.data);
    } catch (error) {
      console.log("ERROR!");
      console.log(error);
    }
  }
}

export default MyNotes;
