// Navigation function
function showSection(sectionId) {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.classList.remove('active'));
    document.getElementById(sectionId).classList.add('active');
    
    // Re-init jQuery bindings after section change (safeguard)
    if (sectionId === 'attendance') {
        setTimeout(() => {
            initJQueryFeatures(); // Call below function
        }, 100);
    }
}

// Toggle presence/participation
function toggleX(button) {
    if (button.textContent.trim() === "") {
        button.textContent = "✅";
    } else {
        button.textContent = "";
    }
    const row = button.closest('tr');
    updateRow(row);
    // Update report if visible
    if (document.getElementById('reportSection').style.display !== 'none') {
        generateReport();
    }
}

// Form validation and add student
document.getElementById('addStudentForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const errors = document.querySelectorAll('.error');
    errors.forEach(err => err.textContent = '');
    document.getElementById('successMessage').style.display = 'none';
    let isValid = true;
    const studentId = document.getElementById('studentid').value.trim();
    if (!studentId || !/^\d+$/.test(studentId)) {
        document.getElementById('err-studentid').textContent = 'Student ID must be non-empty and contain only numbers.';
        isValid = false;
    }
    const lastName = document.getElementById('lastname').value.trim();
    if (!lastName || !/^[a-zA-Z]+$/.test(lastName)) {
        document.getElementById('err-lastname').textContent = 'Last Name must contain only letters.';
        isValid = false;
    }
    const firstName = document.getElementById('firstname').value.trim();
    if (!firstName || !/^[a-zA-Z]+$/.test(firstName)) {
        document.getElementById('err-firstname').textContent = 'First Name must contain only letters.';
        isValid = false;
    }
    const course = document.getElementById('course').value.trim();
    if (!course) {
        document.getElementById('err-course').textContent = 'Course must not be empty.';
        isValid = false;
    }
    const email = document.getElementById('email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
        document.getElementById('err-email').textContent = 'Email must follow a valid format (e.g., name@example.com).';
        isValid = false;
    }
    if (isValid) {
        const table = document.querySelector('#attendanceTable');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${lastName}</td>
            <td>${firstName}</td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td><button onclick="toggleX(this)"></button></td>
            <td>6 Abs</td>
            <td>0 Par</td>
            <td></td>
        `;
        table.appendChild(newRow);
        updateRow(newRow);
        // Add data-student to new row
        $(newRow).attr('data-student', '');
        if (document.getElementById('reportSection').style.display !== 'none') {
            generateReport();
        }
        document.getElementById('successMessage').style.display = 'block';
        this.reset();
    }
});

// Update row calculations (absences, participation, color, message)
function updateRow(row) {
    const pIndices = [2, 4, 6, 8, 10, 12];
    const paIndices = [3, 5, 7, 9, 11, 13];
    let absences = 0;
    for (let i of pIndices) {
        const btn = row.cells[i].querySelector('button');
        if (btn.textContent.trim() == '') {
            absences++;
        }
    }
    let participation = 0;
    for (let i of paIndices) {
        const btn = row.cells[i].querySelector('button');
        if (btn.textContent.trim() === '✅') {
            participation++;
        }
    }
    row.cells[14].textContent = absences + ' Abs';
    row.cells[15].textContent = participation + ' Par';
    let color;
    if (absences < 3) {
        color = '#B4DEBD';
    } else if (absences <= 4) {
        color = '#FFFD8F';
    } else {
        color = '#BF092F';
    }
    row.style.backgroundColor = color;
    let msg;
    if (absences >= 5) {
        msg = 'Excluded, too many absences. You need to participate more.';
    } else if (absences >= 3) {
        msg = 'Warning, attendance low. You need to participate more.';
    } else {
        if (participation >= 4) {
            msg = 'Good attendance, Excellent participation.';
        } else {
            msg = 'Good attendance, You need to participate more.';
        }
    }
    row.cells[16].textContent = msg;
}

// Generate report
function generateReport() {
    const table = document.getElementById('attendanceTable');
    const dataRows = Array.from(table.querySelectorAll('tr')).filter(row => row.cells[0] && row.cells[0].tagName === 'TD');
    const totalStudents = dataRows.length;
    let totalPresent = 0;
    let totalParticipated = 0;
    const pIndices = [2, 4, 6, 8, 10, 12];
    const paIndices = [3, 5, 7, 9, 11, 13];
    dataRows.forEach(row => {
        pIndices.forEach(i => {
            const btn = row.cells[i].querySelector('button');
            if (btn.textContent.trim() === '✅') {
                totalPresent++;
            }
        });
        paIndices.forEach(i => {
            const btn = row.cells[i].querySelector('button');
            if (btn.textContent.trim() === '✅') {
                totalParticipated++;
            }
        });
    });
    document.getElementById('totalStudents').textContent = `Total Number of Students: ${totalStudents}`;
    document.getElementById('totalPresent').textContent = `Number of Students Marked Present (Total Presences): ${totalPresent}`;
    document.getElementById('totalParticipated').textContent = `Number of Students Marked as Having Participated (Total Participations): ${totalParticipated}`;
    const maxValue = Math.max(totalStudents, totalPresent, totalParticipated, 1);
    const barHeightScale = 180 / maxValue;
    document.getElementById('totalStudentsBar').innerHTML = totalStudents;
    document.getElementById('totalStudentsBar').style.height = (totalStudents * barHeightScale) + 'px';
    document.getElementById('totalPresentBar').innerHTML = totalPresent;
    document.getElementById('totalPresentBar').style.height = (totalPresent * barHeightScale) + 'px';
    document.getElementById('totalParticipatedBar').innerHTML = totalParticipated;
    document.getElementById('totalParticipatedBar').style.height = (totalParticipated * barHeightScale) + 'px';
    document.getElementById('reportSection').style.display = 'block';
    document.getElementById('reportSection').scrollIntoView({ behavior: 'smooth' });
}

// NEW: Update sort message
function updateSortMessage(mode) {
    if (mode === 'none') {
        $('#sortMessage').text('');
    } else {
        $('#sortMessage').text(`Currently sorted by ${mode}.`);
    }
}

// Centralized jQuery init function (for re-binding)
function initJQueryFeatures() {
    console.log('Initializing jQuery features...'); // Debug log
    
    // Set data-student on all data rows
    $('#attendanceTable tr').each(function () {
        if ($(this).find('td').length > 0 && !$(this).attr('data-student')) {
            $(this).attr('data-student', '');
        }
    });
    console.log('Data-student attributes set on', $('#attendanceTable tr[data-student]').length, 'rows.'); // Debug

    // Exercise 5: Hover & Click
    $('#attendanceTable').off('mouseenter mouseleave click').on('mouseenter', 'tr[data-student]', function () {
        $(this).addClass('hover');
    }).on('mouseleave', 'tr[data-student]', function () {
        $(this).removeClass('hover');
    }).on('click', 'tr[data-student]', function () {
        const lastName = $(this).find('td').eq(0).text().trim();
        const firstName = $(this).find('td').eq(1).text().trim();
        const absences = $(this).find('td').eq(14).text().trim();
        const fullName = `${firstName} ${lastName}`;
        alert(`${fullName}\nAbsences: ${absences}`);
    });

    // Exercise 6: Buttons (re-bind)
    $('#highlightExcellentBtn').off('click').on('click', function () {
        console.log('Highlight button clicked!'); // Debug
        $('#attendanceTable tr').removeClass('excellent-highlight');
        let excellentCount = 0;
        $('#attendanceTable tr[data-student]').each(function () {
            const absencesText = $(this).find('td').eq(14).text().trim();
            const absencesNum = parseInt(absencesText.match(/\d+/)?.[0] || '0') || 0; // Safer extract
            console.log('Row absences:', absencesText, '->', absencesNum); // Debug
            if (absencesNum < 3) {
                excellentCount++;
                const $row = $(this);
                $row.addClass('excellent-highlight');
                $row.stop(true, true)
                    .css({ 'background-color': '#d4edda !important', 'transition': 'all 0.6s ease' })
                    .animate({ opacity: 0.7 }, 400)
                    .animate({ opacity: 1 }, 400)
                    .animate({ opacity: 0.7 }, 400)
                    .animate({ opacity: 1 }, 400);
            }
        });
        console.log('Highlighted', excellentCount, 'excellent students.'); // Debug
        alert(excellentCount > 0 ? `Highlighted ${excellentCount} excellent student(s)!` : 'No students with <3 absences yet—mark some "P" buttons!');
    });

    $('#resetColorsBtn').off('click').on('click', function () {
        console.log('Reset button clicked!'); // Debug
        $('#attendanceTable tr')
            .removeClass('excellent-highlight')
            .stop(true, true)
            .css({ 'background-color': '', 'opacity': 1, 'transition': '' });
        // Restore original colors
        $('#attendanceTable tr[data-student]').each(function () {
            updateRow(this);
        });
        console.log('Colors reset.'); // Debug
    });

    // NEW: Exercise 7 - Search by Name (using .filter())
    $('#searchName').off('keyup').on('keyup', function () {
        const value = $(this).val().toLowerCase();
        if (value === '') {
            $('#attendanceTable tr[data-student]').show();
        } else {
            const $rows = $('#attendanceTable tr[data-student]').filter(function () {
                const lastName = $(this).find('td').eq(0).text().toLowerCase();
                const firstName = $(this).find('td').eq(1).text().toLowerCase();
                return lastName.includes(value) || firstName.includes(value);
            });
            $('#attendanceTable tr[data-student]').not($rows).hide();
            $rows.show();
        }
    });

    // NEW: Exercise 7 - Sort by Absences (Ascending)
    $('#sortAbsAscBtn').off('click').on('click', function () {
        const rows = $('#attendanceTable tr[data-student]').get();
        rows.sort(function (a, b) {
            const absA = parseInt($(a).find('td').eq(14).text().match(/\d+/)?.[0] || '0') || 0;
            const absB = parseInt($(b).find('td').eq(14).text().match(/\d+/)?.[0] || '0') || 0;
            return absA - absB;
        });
        $.each(rows, function (index, row) {
            $('#attendanceTable').append(row);
        });
        updateSortMessage('absences (ascending)');
    });

    // NEW: Exercise 7 - Sort by Participation (Descending)
    $('#sortPartDescBtn').off('click').on('click', function () {
        const rows = $('#attendanceTable tr[data-student]').get();
        rows.sort(function (a, b) {
            const partA = parseInt($(a).find('td').eq(15).text().match(/\d+/)?.[0] || '0') || 0;
            const partB = parseInt($(b).find('td').eq(15).text().match(/\d+/)?.[0] || '0') || 0;
            return partB - partA; // Descending
        });
        $.each(rows, function (index, row) {
            $('#attendanceTable').append(row);
        });
        updateSortMessage('participation (descending)');
    });

    // Initial sort message
    updateSortMessage('none');
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function () {
    const allRows = document.querySelectorAll('#attendanceTable tr');
    const dataRows = Array.from(allRows).filter(row =>
        row.cells[0] && row.cells[0].tagName === 'TD'
    );
    dataRows.forEach(updateRow);
    
    // Init jQuery after DOM load
    setTimeout(initJQueryFeatures, 100);
});