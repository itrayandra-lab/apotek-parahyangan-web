import "./libs/trix";
import "./bootstrap";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import Splide from "@splidejs/splide";
import "@splidejs/splide/css";

Alpine.plugin(collapse);
window.Alpine = Alpine;
window.Splide = Splide;
Alpine.start();
