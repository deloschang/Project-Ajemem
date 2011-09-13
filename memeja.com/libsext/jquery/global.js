<!--
	

function validateElement(elmtName)
{	
	elmt = document.getElementsByName(elmtName)[0];
	if(elmt == null)
		return true;
	len = elmt.value.trim().length
	elmt.style.backgroundColor = (len == 0) ? "#FCDFFF" : "white";
	return (len == 0)? false : true;
}

function checkValidity(elemArray)
{
	var valid = true;
	for(i = 0; i < elemArray.length; i++)
	{
		if(!validateElement(elemArray[i])) 
			valid = false;
	}
	getelem('divError').innerHTML = 'One or more required fields are empty! Please fill all highlighted.'
	setDisplay('divError', !valid)
	
	return valid;
}

function checkEmail(id)
{
	var valid = true
	var elmt = document.getElementsByName(id)[0]
	var email = elmt.value
	var emailRegex=/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
	
	if(!email.match(emailRegex))
	{
		getelem('divError').innerHTML = 'Please provide a valid email.'
		valid = false
	}
	
	elmt.style.backgroundColor = (valid == false) ? "#FCDFFF" : "white";
	setDisplay('divError', !valid)
	return valid
}

function checkDate(id)
{
	var valid = true
	var elmt = document.getElementsByName(id)[0]
	var date = elmt.value
	var dateRegex=/\d{2}\/\d{2}\/\d{4}$/

	if(date.length == 0)
		return true

	if(!date.match(dateRegex))
	{
		getelem('divError').innerHTML = 'Please provide a valid date.'
		valid = false
	}
	else
	{
		var dateArray = date.split('/')
		var dateInput = new Date(dateArray[1] + '/' + dateArray[0] + '/' + dateArray[2])
		var todayDate = new Date()
		todayDate = new Date((todayDate.getMonth() + 1) + '/' + todayDate.getDate() + '/' + todayDate.getFullYear() )
		
		if(dateInput < todayDate)
		{
			getelem('divError').innerHTML = 'Date cannot be less than today.'
			valid = false
		}
	}
	
	elmt.style.backgroundColor = (valid == false) ? "#FCDFFF" : "white";
	setDisplay('divError', !valid)	
	return valid
}

function setDisplay(id, show)
{
	if(show == 1)
		getelem(id).style.display = 'block'
	else if(show == 2)
		getelem(id).style.display = 'inline'
	else if(show == 3)
		getelem(id).style.display = 'table-row'
	else
		getelem(id).style.display = 'none'
}

function getelem(id)
{
	return document.getElementById(id)
}

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

function getSelectedValue(id)
{
	var objElem = getelem(id)
	return objElem.options[objElem.selectedIndex].value
}

function getSelectedText(id)
{
	var objElem = getelem(id)
	return objElem.options[objElem.selectedIndex].text
}

function del(id, text, type)
{
	var response = confirm('Are you sure you want to delete ' + type + ' \''+ text +'\'?')
	if(response)
	{
		document.getElementsByName('delete')[0].value = id;
		document.forms[0].submit()
	}
}

function next()
{
	switch(step)
	{
		case 1:
			if(getelem('vacancy').selectedIndex > 0 && !checkValidity(['script-title']))
				return
			else if(getelem('vacancy').selectedIndex == 0 && !checkValidity(['vacancy-job-type', 'vacancy-job-level', 'script-title', 'vacancy-title']))
				return;
			break;
		case 2:
			setDisplay('divBtnNext', 0)	
			break;
	}
	setDisplay('step' + step, 0)
	setDisplay('step' + (step + 1), 1)
	step ++;	
}

function interviewWizardSubmit(type)
{
	document.getElementsByName('interviewType')[0].value = type
	document.forms[0].submit()
}

function setInterviewerEmail(index)
{
	document.getElementsByName('fellow-interviewer-email')[0].value = document.getElementsByName('fellow-interviewer-email-list')[0].options[index].text
}

function printInvoice(url)
{
	window.open(url, 'PrintableScript', 'status=0,location=0,menubar=0,toolbar=0,width=450,height=400,scrollbars=1')
}

function finishToDashboard(val)
{
	var response = 1
	if(val == 0)
	{
		response = confirm('Do you wish to finish without saving this candidate?')
	}
	
	if(val > 0 || response)
		window.location = "index.php?do=dashboard"
}

function addInterviewer()
{
	if(!checkValidity(['txtInterviewer']))
		return
	if(getelem('txtInterviewerEmail').value.length > 0)
	{
		if(!checkEmail('txtInterviewerEmail'))
			return
	}
	
	if(getelem('hiddenInterviewer').value.length > 0)
	{
		getelem('hiddenInterviewer').value += ', '
		getelem('hiddenInterviewerEmail').value += ', '
	}
	
	
	
	getelem('hiddenInterviewer').value += getelem('txtInterviewer').value
	getelem('hiddenInterviewerEmail').value += getelem('txtInterviewerEmail').value
	getelem('spanInterviewer').innerHTML = getelem('hiddenInterviewer').value
	getelem('txtInterviewer').value = ''
	getelem('txtInterviewerEmail').value = ''
}

function selectVacancy()
{
	var show = (getelem('vacancy').selectedIndex == 0)?3:0
	//alert(show);
	setDisplay('tr1', show)
	setDisplay('tr2', show)
	setDisplay('tr3', show)
}

function showVacancyText(ind)
{
	var show = (ind == 0)? 1:0
	setDisplay('vacancy-title', show)
}

function nextCustom(valid)
{
	if(valid)
	{
		setDisplay('stepInterviewer', 1)
		setDisplay('stepCustom1', 0)
	} 
}
//-->
