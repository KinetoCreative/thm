(function() {
  window.addEventListener("load", resize);
  window.addEventListener("resize", resize);

  function resize() {
    const grid = document.querySelector(".grid");
    const rowHeight = getStyleValue(grid, "grid-auto-rows");
    const rowGap = getStyleValue(grid, "grid-row-gap");
    grid.style.gridAutoRows = "auto";
    grid.style.alignItems = "self-start";
    grid.querySelectorAll(".item").forEach(item => {
      item.style.gridRowEnd = `span ${Math.ceil(
        (item.clientHeight + rowGap) / (rowHeight + rowGap)
      )}`;
    });
    grid.removeAttribute("style");
  }

  function getStyleValue(element, style) {
    return parseInt(window.getComputedStyle(element).getPropertyValue(style));
  }
})();