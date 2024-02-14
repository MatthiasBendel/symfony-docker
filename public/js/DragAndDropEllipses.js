const svg = document.querySelector('svg');
let activeEllipse = null;
let offset = { x: 0, y: 0 };

let svg2 = generateCircleOfEllipses(250, 250, 200, 16);
document.getElementById('circleOfEllipses').innerHTML = svg2;

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

    checkForOverlap(activeEllipse)
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
           console.log("found overlap");
           console.log(ellipse);
          // If there is an overlap, replace the active ellipse
          ellipse.setAttribute('cx', Number(ellipse.getAttribute('cx')) + 10);
          ellipse.setAttribute('cy', Number(ellipse.getAttribute('cy')) + 10);

           console.log(ellipse);
          //checkForOverlap(activeEllipse);
          //checkForOverlap(ellipse);
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




function generateCircleOfEllipses(centerX, centerY, radius, numEllipses) {
  let svgCode2 = '<svg width="500" height="500">';
  for (let i = 0; i < numEllipses; i++) {
    let angle = (i / numEllipses) * 2 * Math.PI;
    let x = centerX + radius * -1 * Math.sin(angle);
    let y = centerY + radius * -1 * Math.cos(angle);
    let ellipse = `<ellipse cx="${x}" cy="${y}" rx="20" ry="10" fill="blue" />`;
    svgCode2 += ellipse;
    radius = radius - 5;
  }
  svgCode2 += '</svg>';
  return svgCode2;
}

