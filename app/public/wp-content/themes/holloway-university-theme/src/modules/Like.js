import axios from "axios";

class Like {
  constructor() {
    if (universityData.nonce) {
      axios.defaults.headers.common["X-WP-Nonce"] = universityData.nonce;
    }
    this.events();
  }

  events() {
    const likeBox = document.querySelector(".like-box");
    if (likeBox) {
      likeBox.addEventListener("click", this.clickDispacher.bind(this));
    }
  }

  // custom methods
  clickDispacher(e) {
    if (e.currentTarget.dataset.exists === "yes") {
      this.deleteLike(e);
    } else {
      this.createLike(e);
    }
  }

  async createLike(e) {
    const likeBox = e.currentTarget;
    const professorId = likeBox.dataset.professor;
    try {
      const response = await axios.post(
        universityData.root_url + "/wp-json/university/v1/manageLike",
        {
          liked_professor_id: professorId,
        }
      );
      likeBox.dataset.exists = "yes";
      likeBox.dataset.like = response.data;
      likeBox.querySelector(".fa-heart-o").style.display = "none";
      likeBox.querySelector(".fa-heart").style.display = "inline";
      likeBox.querySelector(".like-count").textContent =
        parseInt(likeBox.querySelector(".like-count").textContent) + 1;
      console.log(response.data);
    } catch (error) {
      console.log(error);
    }
  }

  async deleteLike(e) {
    const likeBox = e.currentTarget;
    const likeId = likeBox.dataset.like;
    const professorId = likeBox.dataset.professor;
    try {
      const response = await axios.delete(
        universityData.root_url + "/wp-json/university/v1/manageLike",
        {
          data: {
            like_id: likeId,
            liked_professor_id: professorId,
          },
        }
      );
      likeBox.dataset.exists = "no";
      likeBox.dataset.like = "";
      likeBox.querySelector(".fa-heart-o").style.display = "inline";
      likeBox.querySelector(".fa-heart").style.display = "none";
      likeBox.querySelector(".like-count").textContent =
        parseInt(likeBox.querySelector(".like-count").textContent) - 1;
      console.log(response.data);
    } catch (error) {
      console.log(error);
    }
  }
}

export default Like;
