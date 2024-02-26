// Select the SVG text element
const svgText = document.getElementById('inputText');
var movingDirection;

function createSVGEllipseWithText(svg) {
  var text = svgText.textContent.slice(0, -4);
  var cy = Math.random() * parseInt(svg.getAttribute('height'));
  createSVGEllipseText(svg, text, 0, 0, cy);
  console.log('this is text: ' + text);
  removeAndRecreateOverlappingElements(svg, text);
}

function removeAndRecreateOverlappingElements(svg, text) {
  let overlapping = true;
  while (overlapping) {
    let elements = svg.querySelectorAll('ellipse, text');
    let elementsArray = Array.from(elements); // Convert the NodeList to an array
    let element = svg.querySelector('#' + text);
    console.log(elementsArray);
    console.log("Searching for overlap with " + text);
    if (element != null) {
      for (let i = 0; i < elementsArray.length; i++) {
         if (doOverlap(elementsArray[i], element)) {
            if(elementsArray[i] =! element && element != null) {
             // Elements overlap
             console.log("Found overlap for " + element + ". Removing " + elementsArray[i]);
             element2.remove(); // Remove the overlapping element
           }
         } else {
         }
       }
    }
    overlapping = false
  }
}

function doOverlap(element1, element2) {
  const rect1 = element1.getBoundingClientRect();
  const rect2 = element2.getBoundingClientRect();
  return !(
    rect1.right < rect2.left ||
    rect1.left > rect2.right ||
    rect1.bottom < rect2.top ||
    rect1.top > rect2.bottom
  );
}

function createSVGEllipseText(svg, text, leftWeight, rightWeight, cy) {
  console.log("Create ellipse for: " + text);
  var svgWidth = svg.getAttribute('width');

  // Generate random rx value
  var rx = (100 * (text.length / 5)).toString();
  var ry = 40;
  var strokeWidth = 1;

  // Generate random cx and cy values within the range of the SVG dimensions
  var cx = rx + (leftWeight * Math.random() * (svgWidth - 2*rx) / parseInt((leftWeight + rightWeight)+1)) + leftWeight;

  // Create the ellipse and text elements
  var ellipse = document.createElementNS("http://www.w3.org/2000/svg", "ellipse");
  ellipse.setAttribute("cx", cx.toString());
  ellipse.setAttribute("cy", cy.toString());
  ellipse.setAttribute("rx", rx);
  ellipse.setAttribute("ry", ry.toString());
  ellipse.setAttribute("fill", "grey");
  ellipse.setAttribute("stroke", "white");
  ellipse.setAttribute("stroke-width", strokeWidth.toString());
  ellipse.setAttribute("id", text);

  var textElement = document.createElementNS("http://www.w3.org/2000/svg", "text");
  textElement.setAttribute("x", cx.toString());
  textElement.setAttribute("y", (cy + 5).toString()); // Adjust the Y position for the text
  textElement.setAttribute("text-anchor", "middle"); // Center the text horizontally
  textElement.setAttribute("font-size", "36");
  textElement.setAttribute("font-family", 'Courier New');
  textElement.textContent = text;
  textElement.setAttribute("id", text);

  // Append the ellipse and text to the existing "svg" element
  svg.appendChild(ellipse);
  svg.appendChild(textElement);
}

// Add an event listener to the document to listen for keyboard input
var meta = false;
document.addEventListener('keydown', async () => {
  console.log("Received input: " + event.key);
  if (event.key == 'Enter') {
      createSVGEllipseWithText(document.getElementById('svg'));
      svgText.textContent = " ..."
  } else if (event.key == 'ArrowUp' || event.key == 'ArrowDown' || event.key == 'ArrowLeft' || event.key == 'ArrowRight') {
    moveSelected(event.key);
  } else if (event.key == 'Backspace') {
    svgText.textContent = svgText.textContent.slice(0, -5) + " ..."
  } else if (event.key == 'Shift') {
  } else if (event.key == 'Alt') {
  } else if (event.key == 'Shift') {
  } else if (event.key == 'Tab') {
  } else if (event.key == 'Control') {
  } else if (event.key == 'Meta') {
    meta = true;
  } else if (meta && event.key == 'v') {
      try {
        const text = await navigator.clipboard.readText();
        svgText.textContent = svgText.textContent.slice(0, -9) + text + " ...";
        console.log('Text from clipboard: ', text);
        meta = false;
      } catch (err) {
        console.error('Failed to read from clipboard: ', err);
      }
  } else {
    svgText.textContent = svgText.textContent.slice(0, -4) + event.key + " ...";
  }
});

function moveSelected(key) {
//  const textElement = document.querySelector('text.1_Hello');
  //const elements = document.getElementsByClassName('1_Hello');
  const elements = document.getElementsByClassName(selectedId);
  console.log(selectedId);
  movingDirection = key;
  if (key == 'ArrowUp') {
    moveElements(elements, -0.5, -0.5);
  }
  if (key == 'ArrowLeft') {
    moveElements(elements, -0.5, 0.5);
  }
  if (key == 'ArrowDown') {
    moveElements(elements, 0.5, 0.5);
  }
  if (key == 'ArrowRight') {
    moveElements(elements, 0.5, -0.5);
  }
}

function moveElements(elements, x_delta, y_delta) {
  for (var i = 0; i < elements.length; i++) {
    if (elements[i].tagName == 'ellipse') {
      ellipse = elements[i];
      console.log("ellipse was set to:")
      console.log(ellipse);
    }
    if (elements[i].tagName == 'text') {
      text = elements[i];
    }
  }
  delta_x = x_delta;
  delta_y = y_delta;
}

function continueMoving(cx, cy, rx, ry) {
  if (movingDirection == 'ArrowUp') {
    return cx > rx && cy > ry;
  }
  if (movingDirection == 'ArrowLeft') {
    return cx > rx && cy < 600 ;
  }
  if (movingDirection == 'ArrowDown') {
    return cx < 600 && cy < 600;
  }
  if (movingDirection == 'ArrowRight') {
    return cy > ry && cx < 600;
  }
  if (Math.abs(end_x - cx) < 1 && Math.abs(end_y - cy) < 1) {
    return false;
  }
  //console.log("Always continueMoving!");
  return true;
}

// - - - - - - - - - - ENTITY-ITERATOR - - - - - - - - - - - -

var entityIterator = 0
function placeNextEntity() {
  console.log(entities);
  var i = 0;
  for (const entityKey in entities) {
    if (i == entityIterator) {
        const svgElements = document.getElementsByClassName(entityKey);
        selectedId = entityKey;
        end_x = entities[entityKey]['x'];
        end_y = entities[entityKey]['y'];
        if ((typeof end_x === 'string' || end_x instanceof String) && end_x.endsWith("%")) {
          end_x = Number(end_x.replace("%", "")) * 600 / 100;
        }
        if ((typeof end_y === 'string' || end_y instanceof String) && end_y.endsWith("%")) {
          end_y = Number(end_y.replace("%", "")) * 600 / 100;
        }
        //console.log(svgElements[0].cx);
        const elements = document.getElementsByClassName(selectedId);
        moveElements(elements, 0, 0);
        text.style.textDecoration = 'underline';

        console.log("Started Iteration: " + entityKey);
    }
    i++;
  }
}
placeNextEntity();

// - - - - - - - - - - GRAVITY - - - - - - - - - - - -
setInterval(gravity, 10);

var delta_x;
var delta_y;
var ellipse;
var text;
var selectedId;
var end_x;
var end_y;
function gravity() {
  //console.log("ToDo: bring selected element to end coordinates");
  if (ellipse != null) {
    console.log("ellipse is set. Going to proceed " + selectedId);
    if (end_x != null) {
      delta_x = (end_x - Number(ellipse.getAttribute('cx'))) / 100;
      delta_y = (end_y - Number(ellipse.getAttribute('cy'))) / 100;
    }
    cx = Number(ellipse.getAttribute('cx')) + delta_x;
    cy = Number(ellipse.getAttribute('cy')) + delta_y;
    x = parseFloat(text.getAttribute('x')) + delta_x;
    y = parseFloat(text.getAttribute('y')) + delta_y;

  console.log("Continue moving! with movingDirection:" + movingDirection);
    if (continueMoving(cx, cy, ellipse.getAttribute('rx'), ellipse.getAttribute('ry'))) {
      ellipse.setAttribute('cx', cx);
      ellipse.setAttribute('cy', cy);
      text.setAttribute('x', x);
      text.setAttribute('y', y);
    } else {
      finishedMoving();
    }
  }
}

function finishedMoving() {
  text.style.textDecoration = 'none';
  console.log("Finished moving! with movingDirection:" + movingDirection);
  movingDirection == null;
  ellipse = null;
  text = null;
  entityIterator++;
  placeNextEntity();
}
