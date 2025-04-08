@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="text-center mb-5">
      <img src="img/logo.JPG" alt="Logo" class="d-block mx-auto logo" style="max-width: 200px;">
      <h2>CUSTOMER SATISFACTION SURVEY FORM</h2>
    </div>

    <form id="surveyForm">
      <!-- Account Info Section -->
      <div class="row mb-4">
        <div class="col-md-4 col-12">
          <label for="accountName" class="form-label">Account Name</label>
          <input type="text" class="form-control" id="accountName" name="accountName" required>
        </div>
        <div class="col-md-4 col-12">
          <label for="accountType" class="form-label">Account Type</label>
          <input type="text" class="form-control" id="accountType" name="accountType" required>
        </div>
        <div class="col-md-4 col-12">
          <label for="date" class="form-label">Date</label>
          <input type="date" class="form-control" id="date" name="date" required>
        </div>
      </div>

      <!-- Survey Questions Section -->
      <h6 class="text-center mt-4 mb-4">Please select the number which more accurately reflects your satisfaction level</h6>

      <table class="table table-bordered survey-table">
        <thead class="text-center">
          <tr>
            <th>Questions</th>
            <th>1-Poor</th>
            <th>2-Needs Improvement</th>
            <th>3-Satisfactory</th>
            <th>4-Very Satisfactory</th>
            <th>5-Excellent</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Our salesperson is courteous and well-groomed</td>
            <td><input type="radio" name="wellGroomed" value="1" required></td>
            <td><input type="radio" name="wellGroomed" value="2" required></td>
            <td><input type="radio" name="wellGroomed" value="3" required></td>
            <td><input type="radio" name="wellGroomed" value="4" required></td>
            <td><input type="radio" name="wellGroomed" value="5" required></td>
          </tr>
          <tr>
            <td>He visits your outlet regularly as scheduled</td>
            <td><input type="radio" name="visitsRegularly" value="1" required></td>
            <td><input type="radio" name="visitsRegularly" value="2" required></td>
            <td><input type="radio" name="visitsRegularly" value="3" required></td>
            <td><input type="radio" name="visitsRegularly" value="4" required></td>
            <td><input type="radio" name="visitsRegularly" value="5" required></td>
          </tr>
          <tr>
            <td>He conducts stock inventory and store checking during a store visit</td>
            <td><input type="radio" name="storeChecking" value="1" required></td>
            <td><input type="radio" name="storeChecking" value="2" required></td>
            <td><input type="radio" name="storeChecking" value="3" required></td>
            <td><input type="radio" name="storeChecking" value="4" required></td>
            <td><input type="radio" name="storeChecking" value="5" required></td>
          </tr>
          <tr>
            <td>Our delivery personnel are well-mannered, polite, and honest</td>
            <td><input type="radio" name="wellMannered" value="1" required></td>
            <td><input type="radio" name="wellMannered" value="2" required></td>
            <td><input type="radio" name="wellMannered" value="3" required></td>
            <td><input type="radio" name="wellMannered" value="4" required></td>
            <td><input type="radio" name="wellMannered" value="5" required></td>
          </tr>
          <tr>
            <td>Ordered stocks are delivered on the expected date and in good condition</td>
            <td><input type="radio" name="onTimeDelivered" value="1" required></td>
            <td><input type="radio" name="onTimeDelivered" value="2" required></td>
            <td><input type="radio" name="onTimeDelivered" value="3" required></td>
            <td><input type="radio" name="onTimeDelivered" value="4" required></td>
            <td><input type="radio" name="onTimeDelivered" value="5" required></td>
          </tr>
          <tr>
            <td>We are responsive to your calls, queries, and/or concerns and provide resolutions on time</td>
            <td><input type="radio" name="responsiveness" value="1" required></td>
            <td><input type="radio" name="responsiveness" value="2" required></td>
            <td><input type="radio" name="responsiveness" value="3" required></td>
            <td><input type="radio" name="responsiveness" value="4" required></td>
            <td><input type="radio" name="responsiveness" value="5" required></td>
          </tr>

        </tbody>
      </table>

      <hr style="height:10px;border-width:0;color:gray;background-color:gray">
      
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-8">
            <p>How likely is it that you would recommend our company to a friend or colleague? Please select a number from 1 to 10</p>
          </div>
          <div class="col-12 col-md-4">
            <select id="survey-number" name="surveyRating" class="form-select form-select-sm" required>
              <option value="" disabled selected>Select a rating</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
            </select>
          </div>
        </div>
      </div>
      <hr style="height:10px;border-width:0;color:gray;background-color:gray">

      <div class="mb-3">
        <textarea class="form-control form-control-m" name="comments" rows="5" placeholder="Please leave a comment..." required></textarea>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-primary">Submit Survey</button>
      </div>
    </form>
  </div>
@endsection
