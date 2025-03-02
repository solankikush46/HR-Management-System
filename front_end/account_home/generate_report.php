<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wage Wizards</title>
    <!-- Link the CSS file -->
    <link rel="stylesheet" href="report.css">
</head>
<body>
    <!-- Back Button -->
    <button class="back-btn" onclick="window.history.back()">Back</button>
    <h1 class="lookup-table-heading">Financial Report</h1>
    
    <div class="financial-report-container">
    <?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "hr_database"; 

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $C_name = $_SESSION['company_name'];
    $sql = "SELECT employs.E_Ssn AS ssn,
        hr.F_name AS fname,
        hr.L_name AS lname,
        pay_details.salary AS salary,
        pay_details.bonus AS bonus,
        pay_details.benefits AS benefits
        FROM employs
        JOIN gets ON employs.E_Ssn = gets.E_Ssn
        JOIN pay_details ON gets.pay_id = pay_details.pay_id
        JOIN hr ON employs.E_Ssn = hr.hr_Ssn
        WHERE employs.C_name = '$C_name'
        UNION
        SELECT employs.E_Ssn AS ssn,
        accountant.F_name AS fname,
        accountant.L_name AS lname,
        pay_details.salary AS salary,
        pay_details.bonus AS bonus,
        pay_details.benefits AS benefits
        FROM employs
        JOIN gets ON employs.E_Ssn = gets.E_Ssn
        JOIN pay_details ON gets.pay_id = pay_details.pay_id
        JOIN accountant ON employs.E_Ssn = accountant.acc_Ssn
        WHERE employs.C_name = '$C_name'
        UNION
        SELECT employs.E_Ssn AS ssn,
        associate.F_name AS fname,
        associate.L_name AS lname,
        pay_details.salary AS salary,
        pay_details.bonus AS bonus,
        pay_details.benefits AS benefits
        FROM employs
        JOIN gets ON employs.E_Ssn = gets.E_Ssn
        JOIN pay_details ON gets.pay_id = pay_details.pay_id
        JOIN associate ON employs.E_Ssn = associate.acc_Ssn
        WHERE employs.C_name = '$C_name'
        ";

    $result = $conn->query($sql);
    if ($result === FALSE) {
        // If there was an error, output the SQL query and the error message
        echo "Error with query: " . $sql . "<br><br><br>";
        echo "MySQL error: " . $conn->error;
    }

    $total_salary = 0;
    $total_bonus = 0;
    $total_benefits = 0;
    if($result->num_rows > 0){
        echo "<table class='lookup-table'><thead><tr><th>SSN</th><th>Employee</th><th>Salary</th><th>Bonus</th><th>Benefits</th><th>Total</th></tr></thead><tbody>";
        while($row = $result->fetch_assoc())
        {
            $total = $row['salary'] + $row['bonus'] + $row['benefits'];
            echo "<tr><td>" . $row['ssn'] . "</td><td>" . $row['fname'] . " " . $row['lname'] . "</td><td>" . $row['salary'] . "</td><td>" . $row['bonus'] . "</td><td>" . $row['benefits'] . "</td><td>$total</td></tr>";
            $total_salary += $row['salary'];
            $total_bonus += $row['bonus'];
            $total_benefits += $row['benefits'];
        }
        $total = $total_salary + $total_bonus + $total_benefits;
        echo "<tr><td></td><td></td><td>$total_salary</td><td>$total_bonus</td><td>$total_benefits</td><td>$total</td></tr>";
        echo "</tbody></table>";
    } else {
        echo "<p>Have your company accountant enter the employees' pay information.</p>";
    }

    $conn->close();
    ?>
    </div>
</body>
</html>