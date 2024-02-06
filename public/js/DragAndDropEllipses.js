// Select all the SVG ellipses
const ellipses = document.querySelectorAll('ellipse');

// Initialize variables for tracking the active ellipse and the offset
let activeEllipse = null;
let offset = { x: 0, y: 0 };

// Function to handle the mousedown event
function handleMouseDown(e) {
  activeEllipse = e.target;
  const bbox = activeEllipse.getBBox();
  offset = {
    x: e.clientX - bbox.x,
    y: e.clientY - bbox.y
  };
}

// Function to handle the mousemove event
function handleMouseMove(e) {
  if (activeEllipse) {
    activeEllipse.setAttribute('cx', e.clientX - offset.x);
    activeEllipse.setAttribute('cy', e.clientY - offset.y);
  }
}

// Function to handle the mouseup event
function handleMouseUp() {
  activeEllipse = null;
}

// Add event listeners to each ellipse
ellipses.forEach(ellipse => {
  ellipse.addEventListener('mousedown', handleMouseDown);
});

// Add global event listeners for mousemove and mouseup
document.addEventListener('mousemove', handleMouseMove);
document.addEventListener('mouseup', handleMouseUp);
