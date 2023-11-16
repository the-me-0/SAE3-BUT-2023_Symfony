actual_stage = "show_all";

// Function that displays the rooms depnend of the stages of the facility
// Stages : List of stages of the facility
// Stage : Stage we want to display
function display(stages, stage) {
  let rowStage;
  for (var i = 0; i !== stages.length + 1; i++) {
    // check if the stage exist
    if (stages[i] !== undefined) {
      rowStage = document.getElementById(stages[i]);
      // if the stage is the same as the stage of the room, display the room
      if (stages[i] === stage) {
        rowStage.style.display = 'block';
      }
      // else, hide the room
      else {
        rowStage.style.display = 'none';
      }
    }
  }
  // check if there is not already a stage button colored if not uncolor it 
  if(actual_stage !== null && actual_stage !== stage) {
    // check if the button wich is colored is the show all button
    if(actual_stage === "show_all"){
      (document.getElementById("show_all")).style.backgroundColor = 'white';
      (document.getElementById("show_all")).style.color = 'black';
    }
    else{
      (document.getElementById("floor_".concat(actual_stage))).style.backgroundColor = 'white';
      (document.getElementById("floor_".concat(actual_stage))).style.color = 'black';
    }
  }
  // color the button of the stage
  buttonToColor = document.getElementById("floor_".concat(stage.toString()));
  buttonToColor.style.backgroundColor = '#386CD9';
  buttonToColor.style.color = 'white';
  // the actual button colored :
  actual_stage = stage;
}

// Function that displays all the rooms
// Stages : List of stages of the facility
function displayAll(stages) {
  // display all stages
  let rowStage;
  for (var i = 0; i !== stages.length; i++) {
    // check if the stage exist
    if (stages[i] !== undefined) {
      // display the stage
      rowStage = document.getElementById((stages[i]).toString());
      rowStage.style.display = 'block';
      // unColor the button of the stage

    }
  }
  // check if there is not already a stage button colored if not uncolor it
  if(actual_stage !== null && actual_stage !== "show_all") {
    (document.getElementById("floor_".concat(actual_stage))).style.backgroundColor = 'white';
    (document.getElementById("floor_".concat(actual_stage))).style.color = 'black';
  }
  buttonToColor = document.getElementById("show_all");
  buttonToColor.style.backgroundColor = '#386CD9';
  buttonToColor.style.color = 'white';
  // the actual button colored
  actual_stage = "show_all";
}



