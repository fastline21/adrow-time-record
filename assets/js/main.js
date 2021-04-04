// Get all employees in php file using fetch api
let showEmployees = [];
const ip = location.host;
fetch(`http://${ip}/adrow-time-record/list-employees.php`)
	.then((response) => {
		return response.json();
	})
	.then((data) => {
		showEmployees.push(...data);
	});

// Show employee using typing fullname
const inputFullname = document.querySelector('#inputFullname');
if (inputFullname) {
	inputFullname.addEventListener('input', (e) => {
		const fullname = e.target.value;
		const selectedEmployees = showEmployees.filter(
			(employee) =>
				fullname !== '' &&
				employee.fullname.toLowerCase().includes(fullname.toLowerCase())
		);
		let ul = document.getElementById('employees');
		ul.innerHTML = '';
		selectedEmployees.forEach((value) => {
			let li = document.createElement('li');
			let link = document.createElement('a');
			link.setAttribute('href', '#');
			link.setAttribute('class', 'nav-link');
			link.setAttribute('value', value.id);
			link.appendChild(document.createTextNode(value.fullname));
			li.appendChild(link);
			li.setAttribute('class', 'nav-item');
			ul.appendChild(li);
		});
	});
}

// Click showed employees
const employees = document.getElementById('employees');
if (employees) {
	employees.addEventListener('click', (e) => {
		e.preventDefault();
		e.target.text !== undefined
			? (inputFullname.value = e.target.text)
			: '';
		let ul = document.getElementById('employees');
		ul.innerHTML = '';
	});
}

// Automatic close alert after 4 second
const alertNode = document.querySelector('.alert');
if (alertNode) {
	const alert = new bootstrap.Alert(alertNode);
	window.setTimeout(() => {
		alert.close();
	}, 4000);
}

// Current Date & Time with interval of 1 second
const timeDisplay = document.getElementsByClassName('time');
function refreshTime() {
	const dateString = new Date().toLocaleString('en-US', {
		timeZone: 'Asia/Manila',
		year: 'numeric',
		month: '2-digit',
		day: '2-digit',
		hour: '2-digit',
		minute: '2-digit',
		second: '2-digit',
	});
	const formattedString = dateString.replace(', ', ' - ');
	for (const [key, value] of Object.entries(timeDisplay)) {
		value.innerHTML = formattedString;
	}
	// timeDisplay.forEach((element) => (element.innerHTML = formattedString));
	// timeDisplay.innerHTML = formattedString;
}
setInterval(refreshTime, 1000);

// Add Employee Modal
const addEmployeeModal = document.querySelector('#addEmployeeModal');
if (addEmployeeModal) {
	addEmployeeModal.addEventListener('hidden.bs.modal', (e) => {
		const modal = e.target.querySelector('.modal-body');
		// All input type
		modal.querySelectorAll('input').forEach((element) => {
			element.value = '';
		});

		// Select type
		modal.querySelector('select').selectedIndex = 0;
	});
}

// Print Time Record
const printTimeRecord = document.querySelector('#printTimeRecord');
if (printTimeRecord) {
	printTimeRecord.addEventListener('click', (e) => {
		const timeRecordTable = document.getElementById('timeRecordTable');
		const newTitle = document.createElement('h1');
		const newReportID = document.createElement('p');
		const newBody = document.createElement('div');
		const newBr = document.createElement('br');
		const cssStyle = document.createElement('style');
		newTitle.innerHTML = 'Adrow Creatives Inc. Time Record';
		let newDate = new Date();
		newDate = newDate
			.toISOString()
			.replace('T', '-')
			.replace(':', '')
			.replace('.', '')
			.replace(':', '')
			.replace('Z', '');
		newReportID.innerHTML = 'Report ID: ' + newDate;
		cssStyle.innerHTML = `
		* {
			padding: 0;
			margin: 0;
		}
		body {
			font-family: Arial, Helvetica, sans-serif;
		}
		.table {
			--bs-table-bg: transparent;
			--bs-table-striped-color: #212529;
			--bs-table-striped-bg: rgba(0, 0, 0, 0.05);
			--bs-table-active-color: #212529;
			--bs-table-active-bg: rgba(0, 0, 0, 0.1);
			--bs-table-hover-color: #212529;
			--bs-table-hover-bg: rgba(0, 0, 0, 0.075);
			width: 100%;
			margin-bottom: 1rem;
			color: #212529;
			vertical-align: top;
			border-color: #dee2e6;
		}
		table {
			caption-side: bottom;
			border-collapse: collapse;
		}
		.table>thead {
			vertical-align: bottom;
		}
		.table>tbody {
			vertical-align: inherit;
		}
		tbody, td, tfoot, th, thead, tr {
			border-color: inherit;
			border-style: solid;
			border-width: 0;
		}
		.table>:not(caption)>*>* {
			padding: .5rem .5rem;
			background-color: var(--bs-table-bg);
			border-bottom-width: 1px;
			box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
		}
		th {
			text-align: inherit;
			text-align: -webkit-match-parent;
		}
		.table-bordered>:not(caption)>* {
			border-width: 1px 0;
		}
		.table-bordered>:not(caption)>*>* {
			border-width: 0 1px;
		}`;
		newBody.append(newTitle);
		newBody.append(newReportID);
		newBody.append(newBr);
		newBody.append(timeRecordTable);
		window.frames['print_frame'].document.head.appendChild(cssStyle);
		window.frames['print_frame'].document.body.innerHTML =
			newBody.innerHTML;
		window.frames['print_frame'].window.focus();
		window.frames['print_frame'].window.print();
	});
}
