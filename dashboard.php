<?php 
session_start();

include "header.php";
if(!isset($_SESSION["user_name"]))
{
    ?>
        <script type="text/javascript">
            window.location.href = "login.php";
        </script>
    <?php
}
?>


        <div class="row" style="">

            <div class="col-lg-6 col-lg-push-3" style="min-height: 500px; background-color: White;">
                <br>
                <!--- Start Editing --->
                <div class="row">

                <br>
                <div class="col-lg-2 col-lg-push-10">

                    <div id="current_que" style="float:left">0</div>
                    <div  style="float:left">/</div>
                    <div id="total_que" style="float:left">0</div>

                </div>

                <div class="row" style="margin: top 30px;">
                    <div class="row">
                        <div class="col-lg-10 col-lg-push-1" style="min-height: 300px; background-color:white" id="load_questions">

                        </div>

                    </div>
                </div>

                <div class="row" style="margin-top:10px">
                <div class="col-lg-6 col-lg-push-3" style="min-height: 50px;">
                     
                        <div class="col-lg-12 text-center">
                             <input type="button" class="btn btn-warning" value="Previous" onclick="load_previous();">&nbsp;
                            <input type="button" class="btn btn-success" value="Next" onclick="load_next();">&nbsp;
                            <input type="button" id="flag" class="btn btn-danger" value="Flag">&nbsp;
                            <input type="button" id="show-flagged" class="btn btn-show_flag" value="show flag" style="color: darksalmon">&nbsp;
                        </div>
                         
                        

                    </div>
                </div>

                </div>

<!--- End Editing --->
            </div>

            
           

        </div>

        <script type="text/javascript">
    var totalQuestions = 0;

    function load_total_que() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                totalQuestions = parseInt(xmlhttp.responseText, 10);
                document.getElementById("total_que").innerHTML = totalQuestions;
                cleanupInvalidFlaggedQuestions();
            }
        };
        xmlhttp.open("GET", "forajax/load_total_que.php", true);
        xmlhttp.send(null);
    }

    var questionno = "1";
    load_questions(questionno);

    function load_questions(questionno) {
        document.getElementById("current_que").innerHTML = questionno;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                if (xmlhttp.responseText == "over") {
                    window.location = "result.php";
                } else {
                    document.getElementById("load_questions").innerHTML = xmlhttp.responseText;
                    load_total_que();
                    updateFlagButton(); // Ensure the flag button is updated when the question is loaded
                }
            }
        };
        xmlhttp.open("GET", "forajax/load_question.php?questionno=" + questionno, true);
        xmlhttp.send(null);
    }

    function radioclick(radiovalue, questionno) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {}
        };
        xmlhttp.open("GET", "forajax/save_answer_in_session.php?questionno=" + questionno + "&value1=" + radiovalue, true);
        xmlhttp.send(null);
    }

    function load_previous() {
        if (questionno == "1") {
            load_questions(questionno);
        } else {
            questionno = parseInt(questionno, 10) - 1;
            load_questions(questionno);
        }
    }

    function load_next() {
        questionno = parseInt(questionno, 10) + 1;
        load_questions(questionno);
    }

    var flaggedQuestions = JSON.parse(localStorage.getItem('flaggedQuestions')) || [];

    function toggleFlag() {
        const questionId = parseInt(document.getElementById("current_que").innerHTML, 10);
        if (flaggedQuestions.includes(questionId)) {
            flaggedQuestions = flaggedQuestions.filter(id => id !== questionId);
        } else {
            if (questionId > 0 && questionId <= totalQuestions) {
                flaggedQuestions.push(questionId);
            }
        }
        localStorage.setItem('flaggedQuestions', JSON.stringify(flaggedQuestions));
        updateFlagButton();
    }

    function updateFlagButton() {
        const questionId = parseInt(document.getElementById("current_que").innerHTML, 10);
        const flagButton = document.getElementById("flag");
        if (flaggedQuestions.includes(questionId)) {
            flagButton.classList.add('flagged');
            flagButton.value = 'Unflag';
            flagButton.innerHTML = '<i class="fas fa-flag"></i> Unflag';
        } else {
            flagButton.classList.remove('flagged');
            flagButton.value = 'Flag';
            flagButton.innerHTML = '<i class="fas fa-flag"></i> Flag';
        }
    }

    function cleanupInvalidFlaggedQuestions() {
        flaggedQuestions = flaggedQuestions.filter(id => id > 0 && id <= totalQuestions);
        localStorage.setItem('flaggedQuestions', JSON.stringify(flaggedQuestions));
    }

    document.getElementById("flag").addEventListener("click", toggleFlag);

    document.getElementById("show-flagged").addEventListener("click", () => {
        cleanupInvalidFlaggedQuestions();
        const flaggedQuestionIds = JSON.parse(localStorage.getItem('flaggedQuestions')) || [];
        alert('Flagged Questions: ' + flaggedQuestionIds.join(', '));
    });

    updateFlagButton(); // Initial call to set the correct state on page load
</script>



       <?php
include "footer.php";
       ?>