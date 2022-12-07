<?php
$title = "About";
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/header.php';
?>

<section id="mainContent">
    <div class="container">
        <div class="row">
            <div class='col-md-10 offset-md-1 text-left'>
                <h2 class="text-center">Objective</h2>
                
                <ul class='style-font'>
                    <li>Enhancing user's productivity and quality of life and to keep track of their daily habits</li>
                    <li>Keep users motivated in pursuing their goals, achieving efficient task management</li>
                    <li>Allowing users to compete and share results with other people</li>
                    <li>Compels user's habit reinforcement</li>
                </ul>            
            </div>
        </div>
        <div class="row">
            <div class='col-md-10 offset-md-1 text-left'>
                <hr>
                <h2 class="text-center">Background</h2>
                <p class="about"> Task management software is ubiquitous since many individuals aim to enhance productivity
                    in their daily life. A typical personal task manager has the following basic functions - task
                    creation and visualisation, configuring task management environments, notification, resource
                    assigning, compatibility and reporting. A typical task over its lifestyle can be described by a
                    finite state machine (IBM Knowledge Centre, n.d.). A personal task manager's major goal is
                    to ensure the user can implement a task successfully - running the task over its lifecycle
                    repeatedly and preventing termination.
                </p>
                <p class="about">
                    Psychologists define habits as “fixed ways of thinking or willining acquired through
                    repetition” (Andrews, 1903). Habits are effectively formed when the automaticity of
                    executing a task or a certain behaviour reaches a certain level. Researchers have shown that
                    around 70-84 days are required to acquire such a level of automaticity (Lally, Jaarsveld,
                    Potts, & Wardle, 2009). In habit formation (Wood & Neal, 2016), which is rendering novel
                    behaviours automatic, there are three indispensable components - the context cue, the
                    repetition of behaviour and the positive reinforcement via rewards. To support habit
                    formation, users need to establish a positive mental experience between the context cue with
                    the implementation of a task. After that, users are aided in reinforcing the association between the cue and the task via repetition. Rewards are provided to strengthen the
                    reinforcement.
                </p>
                <p class="about">
                    Based on the theories listed above, a competent task management tool should be able to
                    satisfy 4 requirements:</p>
                <ol>
                    <li>Implement the basic functions (task creation and visualisation etc.)</li>                    
                    <li>Evolvability - extending the basic functions of a task manager</li>                    
                    <li>Reliable - successfully aid the user to run over a task over its complete life-cycle without termination</li>                    
                    <li>Aid successful habit formation, which relies on the following 3 aspects:</li>

                    <ul>
                        <li>Provide the user with appropriate and effective context cue</li>                
                        <li>Help the user to repeatedly implement a task to the extent of automaticity</li>                        
                        <li>Provide suitable rewards in order to positively reinforce a behaviour</li>
                    </ul>
                </ol>
                <p class="about">
                    It has been observed that current task management tools available focus on only the first
                    requirement, and are weak in the other aspects. In view of this, Habitracker is developed to
                    bridge this gap and serve as a comprehensive task management tool.</p>
            </div>
        </div>
    </div>
</section>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/info_footer.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php';
?>
