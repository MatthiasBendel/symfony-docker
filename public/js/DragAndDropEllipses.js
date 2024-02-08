const svg = document.querySelector('svg');
let activeEllipse = null;
let offset = { x: 0, y: 0 };

function handleMouseDown(e) {
  activeEllipse = e.target;
  const bbox = activeEllipse.getBBox();
  offset = {
    x: e.clientX - bbox.x,
    y: e.clientY - bbox.y
  };
}

function handleMouseMove(e) {
  if (activeEllipse) {
    const bbox = activeEllipse.getBBox();
    activeEllipse.setAttribute('cx', e.clientX - offset.x);
    activeEllipse.setAttribute('cy', e.clientY - offset.y);
  }
}

function handleMouseUp() {
  if (activeEllipse) {
    checkForOverlap(activeEllipse)
    activeEllipse = null;
  }
}

function checkForOverlap(activeEllipse) {
    // Check for overlap with other ellipses
    ellipses.forEach(ellipse => {
      if (ellipse !== activeEllipse) {
        const rect1 = activeEllipse.getBoundingClientRect();
        const rect2 = ellipse.getBoundingClientRect();
        if (!(rect1.right < rect2.left ||
              rect1.left > rect2.right ||
              rect1.bottom < rect2.top ||
              rect1.top > rect2.bottom)) {
          // If there is an overlap, replace the active ellipse
          ellipse.setAttribute('cx', Math.random() * 100);
          ellipse.setAttribute('cy', Math.random() * 100);
          checkForOverlap(activeEllipse);
          checkForOverlap(ellipse);
        }
      }
    });
}


const ellipses = document.querySelectorAll('ellipse');
ellipses.forEach(ellipse => {
  ellipse.addEventListener('mousedown', handleMouseDown);
});

document.addEventListener('mousemove', handleMouseMove);
document.addEventListener('mouseup', handleMouseUp);

// Function to handle the click event on the ellipses
function handleEllipseClick(e) {
  const colorPicker = document.createElement('input');
  colorPicker.setAttribute('type', 'color');
  colorPicker.value = e.target.getAttribute('fill');
  colorPicker.addEventListener('input', function() {
    e.target.setAttribute('fill', colorPicker.value);
  });
  colorPicker.click();
}

// Add event listeners to each ellipse to handle the click event
ellipses.forEach(ellipse => {
  ellipse.addEventListener('click', handleEllipseClick);
});
