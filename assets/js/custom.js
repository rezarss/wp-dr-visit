/////////// Doctors Page
$(document).ready(function () {
  $(".babakhani").persianDatepicker({
    format: "YYYY-MM-DD",
    persianDigit: false,
  });
});
function addShifts(type, drId) {
  let visitPrice = $("#visit-price-" + drId).val();
  let visitTime = $("#average-visit-time-" + drId).val();
  let clinicAddress = $("#clinic-address-" + drId).val();
  let clinicPhone = $("#clinic-phone-" + drId).val();
  let cliniclatitude = $("#clinic-latitude-" + drId).val();
  let clinicLongitude = $("#clinic-longitude-" + drId).val();

  let shiftCountEl = $("#count-" + type + "-shift-" + drId);

  if (visitPrice.length > 0 && visitTime.length > 0) {
    let shiftCount = parseInt(shiftCountEl.val());

    let shiftCountInputChildren = parseInt(
      $("#" + type + "-shift-count-inputs-" + drId).children().length
    );

    if (shiftCount > shiftCountInputChildren) {
      typeInName = type.replaceAll("-", "_");
      for (let i = shiftCountInputChildren + 1; i < shiftCount + 1; i++) {
        $("#" + type + "-shift-count-inputs-" + drId).append(
          '<div id="' +
          type +
          "-shift" +
          i +
          "-" +
          drId +
          '" class="container add-shift mb-2" style="background: rgba(250,250,250,0.9)"><div><p>شیفت ' +
          i +
          '</p><div class="col-md-3 col-sm-12 my-2"><label for="' +
          type +
          "-shift" +
          i +
          "-price-" +
          drId +
          '" class="form-label inp-label">هزینه ویزیت</label><input type="number" id="' +
          type +
          "-shift" +
          i +
          "-price-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_price_" +
          drId +
          '" value="' +
          visitPrice +
          '" class="inp-shift" /></div></div><div class="row d-flex justify-content-center my-3"><div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1"> <label class="form-label inp-label">ساعت شروع</label><input type="time" id="' +
          type +
          "-shift" +
          i +
          "-start-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_start_" +
          drId +
          '" oninput="calculateVisitQty(this)" class="inp-shift" /></div> <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1"><label class="form-label inp-label">ساعت پایان</label><input type="time" id="' +
          type +
          "-shift" +
          i +
          "-end-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_end_" +
          drId +
          '" oninput="calculateVisitQty(this)" class="inp-shift" /></div> <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1"><label class="form-label inp-label">زمان هر ویزیت (دقیقه)</label><input type="number" id="' +
          type +
          "-shift" +
          i +
          "-visit-time-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_visit_time_" +
          drId +
          '" value="' +
          visitTime +
          '" oninput="calQtyOrVisitTime(this)" class="inp-shift" /></div><div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1"> <label class="form-label inp-label">تعداد ویزیت</label><input type="number" min="0" id="' +
          type +
          "-shift" +
          i +
          "-visit-qty-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_visit_qty_" +
          drId +
          '" oninput="calQtyOrVisitTime(this)" class="inp-shift"></div><div><div class="row d-flex justify-content-center my-3"><div class="col-md-6 col-sm-12"><label class="form-label inp-label">آدرس مطب</label><input type="text" id="' +
          type +
          "-shift" +
          i +
          "-address-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_address_" +
          drId +
          '" value="' +
          clinicAddress +
          '" class="inp-shift"></div><div class="col-md-2 col-sm-12"><label class="form-label inp-label">phone</label><input type="text" id="' +
          type +
          "-shift" +
          i +
          "-phone-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_phone_" +
          drId +
          '" value="' +
          clinicPhone +
          '" class="inp-shift"></div><div class="col-md-2 col-sm-12"><label class="form-label inp-label">latitude</label><input type="text" id="' +
          type +
          "-shift" +
          i +
          "-latitude-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_latitude_" +
          drId +
          '" value="' +
          cliniclatitude +
          '" class="inp-shift"></div><div class="col-md-2 col-sm-12"><label class="form-label inp-label">longitude</label><input type="text" id="' +
          type +
          "-shift" +
          i +
          "-longitude-" +
          drId +
          '" name="' +
          typeInName +
          "_shift" +
          i +
          "_longitude_" +
          drId +
          '" value="' +
          clinicLongitude +
          '" class="inp-shift"></div></div></div> </div></div>'
        );
      }
    } else if (
      shiftCount < shiftCountInputChildren ||
      shiftCountEl.val().length == 0
    ) {
      if (shiftCountEl.val().length == 0) shiftCount = 0;
      for (let i = shiftCount + 1; i <= shiftCountInputChildren; i++) {
        $("#" + type + "-shift" + i + "-" + drId).remove();
      }
    }
  } else {
    myalert("ابتدا فیلدهای مشخص شده را تکمیل کنید.");

    shiftCountEl.val("");
    // show errors to user
    let emptyInputs = [];
    let inputs = ["#visit-price-" + drId,
      "#average-visit-time-" + drId];
    inputs.forEach((id, index) => {
      if ($(id).val().length == 0) {
        $(id).css("border", "2px solid red");
        emptyInputs.push(id);
      }
    });
    // scroll to errors
    $("html, body").animate(
      {
        scrollTop: $("#quick-setting-" + drId).offset().top,
      },
      500
    );

    $(emptyInputs[0]).focus();

    // remove red errors
    setTimeout(() => {
      emptyInputs.forEach((id, index) => {
        $(id).css("border", "");
      });
    },
      3000);
  }
}

function addNewDateShift(addToId, drId) {
  let newDateShiftInputValue = $("#new-date-shift-input" + drId).val();

  var today = new Date(moment().locale("fa").format("YYYY-MM-DD")); // Today
  var inputDate = new Date(newDateShiftInputValue); // input date
  today.setHours(0,
    0,
    0,
    0);
  inputDate.setHours(0,
    0,
    0,
    0);

  // compare today with input date
  if (today > inputDate) {
    myalert("لطفا تاریخی بزرگتر یا مساوی امروز را وارد کنید.");
  } else {
    if (
      $(addToId).find("#date-" + newDateShiftInputValue + "-section").length ==
      0
    ) {
      // add new date shift
      if (newDateShiftInputValue == null || newDateShiftInputValue === "")
        $("#new-date-shift-input" + drId).css("border-color", "red");
      else {
        newDateShiftInputValueInName = newDateShiftInputValue.replaceAll(
          "-",
          "_"
        );
        $("#new-date-shift-input" + drId).css("border-color", "");
        $(addToId).append(
          `<div id="date-` +
          newDateShiftInputValue +
          `-section" class="border border1 rounded new-date-shift-bg mb-2 p-2"> <div class="d-flex position-relative"> <div class="position-absolute top-0 start-0 bg-danger rounded px-2 py-1" role='button' onclick="removeElement('#date-` +
          newDateShiftInputValue +
          `-section')"> <span class="text-white">x</span> </div> <div class="dw-title"><span> شیفت </span><span dir="ltr"> ` +
          newDateShiftInputValue +
          ` </span></div> </div> <div class="d-flex mb-3"> <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">ویزیت فعال</span> <div class="d-flex mx-3"> <div class="mx-2"> <input type="radio" name="enable_date_` +
          newDateShiftInputValueInName +
          `_` +
          drId +
          `" value="1"> <label for="html">باز</label><br> </div> <div class="mx-2"> <input type="radio" name="enable_date_` +
          newDateShiftInputValueInName +
          `_` +
          drId +
          `" value="0" checked=""> <label for="css">بسته</label><br> </div> </div> </div> <div class="d-flex mb-3"> <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">تعداد شیفت</span> <input type="number" name="count_date_` +
          newDateShiftInputValueInName +
          `_shift_` +
          drId +
          `" id="count-date-` +
          newDateShiftInputValue +
          `-shift-` +
          drId +
          `" min="0" value="" oninput="addShifts('date-` +
          newDateShiftInputValue +
          `', ` +
          drId +
          `)" class="form-control search-inp text-center mx-1 w-25"> </div> <div id="date-` +
          newDateShiftInputValue +
          `-shift-count-inputs-` +
          drId +
          `"> </div> </div>`
        );
        $("html, body").animate(
          {
            scrollTop: $("#date-" + newDateShiftInputValue + "-section").offset()
            .top,
          },
          500
        );
      }
    } else {
      myalert("برای این تاریخ قبلا شیفت ثبت شده است.", "warning", "dark");
      // scroll to errors
      $("html, body").animate(
        {
          scrollTop: $("#date-" + newDateShiftInputValue + "-section").offset()
          .top,
        },
        500
      );
      // show user the date element
      $("#date-" + newDateShiftInputValue + "-section").addClass("bg-warning");
      setTimeout(() => {
        $("#date-" + newDateShiftInputValue + "-section").removeClass(
          "bg-warning"
        );
      }, 3000);
    }
  }
}

function removeElement(elId) {
  $(elId).remove();
}

function showEdOddEven(thisElId, drId) {
  let edEl = $("#ed-btn-toggle-" + drId);
  let oddEvenEl = $("#odd-even-btn-toggle-" + drId);
  let noneEl = $("#none-btn-toggle-" + drId);

  let edBox = $("#ed-box-" + drId);
  let evenBox = $("#even-box-" + drId);
  let oddBox = $("#odd-box-" + drId);

  if (thisElId == "ed-btn-toggle") {
    if (edEl.hasClass("btn-outline-primary")) {
      edEl.removeClass("btn-outline-primary");
      edEl.addClass("btn-primary edoddeven-" + drId);
      oddEvenEl.removeClass("btn-primary edoddeven-" + drId);
      oddEvenEl.addClass("btn-outline-primary");
      noneEl.removeClass("btn-primary edoddeven-" + drId);
      noneEl.addClass("btn-outline-primary");
    }
    evenBox.hide();
    oddBox.hide();
    edBox.show();
  } else if (thisElId == "odd-even-btn-toggle") {
    if (oddEvenEl.hasClass("btn-outline-primary")) {
      oddEvenEl.removeClass("btn-outline-primary");
      oddEvenEl.addClass("btn-primary edoddeven-" + drId);
      edEl.removeClass("btn-primary edoddeven-" + drId);
      edEl.addClass("btn-outline-primary");
      noneEl.removeClass("btn-primary edoddeven-" + drId);
      noneEl.addClass("btn-outline-primary");
    }
    edBox.hide();
    evenBox.show();
    oddBox.show();
  } else if (thisElId == "none-btn-toggle") {
    if (noneEl.hasClass("btn-outline-primary")) {
      noneEl.removeClass("btn-outline-primary");
      noneEl.addClass("btn-primary edoddeven-" + drId);
      edEl.removeClass("btn-primary edoddeven-" + drId);
      edEl.addClass("btn-outline-primary");
      oddEvenEl.removeClass("btn-primary edoddeven-" + drId);
      oddEvenEl.addClass("btn-outline-primary");
    }
    edBox.hide();
    evenBox.hide();
    oddBox.hide();
  }
}

function calculateVisitQty(e) {
  let currentId = $(e).attr("id");
  let currKeyId;
  let oppKeyId;

  if (currentId.includes("start")) {
    currKeyId = "start";
    oppKeyId = "end";
  } else {
    currKeyId = "end";
    oppKeyId = "start";
  }

  let oppId = currentId.replace(currKeyId, oppKeyId);
  let visitQtyId = currentId.replace(currKeyId, "visit-qty");
  let visitTimeId = currentId.replace(currKeyId, "visit-time");

  let oppValue = $("#" + oppId).val();

  if (oppValue.length != 0) {
    let time1 = new Date("01/01/2001 " + $(e).val());
    let time2 = new Date("01/01/2001 " + oppValue);

    let shiftLength = Math.abs(time2 - time1) / 1000 / 60;

    let visitQty = shiftLength / $("#" + visitTimeId).val();

    $("#" + visitQtyId).val(Math.round(visitQty));
  }
}

function calQtyOrVisitTime(e) {
  let currentId = $(e).attr("id");
  let currKeyId;
  let oppKeyId;

  if (currentId.includes("time")) {
    currKeyId = "time";
    oppKeyId = "qty";
  } else {
    currKeyId = "qty";
    oppKeyId = "time";
  }

  let oppId = currentId.replace(currKeyId, oppKeyId);
  let oppValue = $("#" + oppId).val();
  let currentValue = $("#" + currentId).val();

  let startTimeId = currentId.replace("visit-" + currKeyId, "start");
  let endTimeId = currentId.replace("visit-" + currKeyId, "end");
  let startTimeValue = $("#" + startTimeId).val();
  let endTimeValue = $("#" + endTimeId).val();

  if (startTimeValue.length > 0 && endTimeValue.length > 0) {
    console.log(
      "currentValue: " +
      currentValue +
      " oppValue: " +
      oppValue +
      " startTimeValue: " +
      startTimeValue +
      " endTimeValue: " +
      endTimeValue
    );

    let time1 = new Date("01/01/2001 " + startTimeValue);
    let time2 = new Date("01/01/2001 " + endTimeValue);

    let shiftLength = Math.abs(time2 - time1) / 1000 / 60;

    console.log(shiftLength);

    let newOppValue = shiftLength / currentValue;

    $("#" + oppId).val(Math.round(newOppValue));
  }
}

function checkBeforeSubmit(drId) {
  if (
    $(".ed-odd-even-div-" + drId).find("div.edoddeven-" + drId).length !== 0
  ) {
    let id = $(".edoddeven-" + drId).attr("id");

    if (id.split("-")[0] === "ed") {
      $("#even-box-" + drId)
      .find("input[type=text], input[type=number]")
      .val("");
      $("#even-shift-count-inputs-" + drId).empty();
      $("input[name=enable_even_" + drId + "][value=0]").prop("checked", true);

      $("#odd-box-" + drId)
      .find("input[type=text], input[type=number]")
      .val("");
      $("#odd-shift-count-inputs-" + drId).empty();
      $("input[name=enable_odd_" + drId + "][value=0]").prop("checked", true);
    } else if (id.split("-")[0] === "odd") {
      $("#ed-box-" + drId)
      .find("input[type=text], input[type=number]")
      .val("");
      $("#ed-shift-count-inputs-" + drId).empty();
      $("input[name=enable_ed_" + drId + "][value=0]").prop("checked", true);

      $("input[name=enable_ed_" + drId + "][value=0]").prop("checked", true);
    } else if (id.split("-")[0] === "none") {
      $("#ed-box-" + drId)
      .find("input[type=text], input[type=number]")
      .val("");
      $("#ed-shift-count-inputs-" + drId).empty();
      $("input[name=enable_ed_" + drId + "][value=0]").prop("checked", true);

      $("#even-box-" + drId)
      .find("input[type=text], input[type=number]")
      .val("");
      $("#even-shift-count-inputs-" + drId).empty();
      $("input[name=enable_even_" + drId + "][value=0]").prop("checked", true);

      $("#odd-box-" + drId)
      .find("input[type=text], input[type=number]")
      .val("");
      $("#odd-shift-count-inputs-" + drId).empty();
      $("input[name=enable_odd_" + drId + "][value=0]").prop("checked", true);
    }
  }

  //// change every open(value 1) radio input to از قبل تعیین شده(value2) if
  if (
    $(".ed-odd-even-div-" + drId).find("div.edoddeven-" + drId).length !== 0
  ) {
    let id = $(".edoddeven-" + drId).attr("id");

    let weekdays = [
      "saturday",
      "sunday",
      "monday",
      "tuesday",
      "wednesday",
      "thursday",
      "friday",
    ];

    if (id.split("-")[0] === "ed") {
      weekdays.forEach(function (wd) {
        let radioValue = $(
          "input[name='enable_weekday_" + wd + "_" + drId + "']:checked"
        ).val();
        console.log(id.split("-")[0], radioValue);
        if (typeof radioValue === "undefined") {
          $(
            "input[name='enable_weekday_" + wd + "_" + drId + "'][value=2]"
          ).prop("checked", true);
        }
      });
    } else if (id.split("-")[0] === "odd") {
      //$("#weekday-box-"+drId).find("input[type=radio][value=2]").prop("checked", true);
      weekdays.forEach(function (wd) {
        let radioValue = $(
          "input[name='enable_weekday_" + wd + "_" + drId + "']:checked"
        ).val();
        console.log(id.split("-")[0], radioValue);
        if (typeof radioValue === "undefined") {
          $(
            "input[name='enable_weekday_" + wd + "_" + drId + "'][value=2]"
          ).prop("checked", true);
        }
      });
    } else if (id.split("-")[0] === "none") {
      weekdays.forEach(function (wd) {
        let radioValue = $(
          "input[name='enable_weekday_" + wd + "_" + drId + "']:checked"
        ).val();
        console.log(id.split("-")[0], radioValue);
        if (radioValue === "2" || typeof radioValue === "undefined") {
          $(
            "input[name='enable_weekday_" + wd + "_" + drId + "'][value=0]"
          ).prop("checked", true);
        }
      });
    }
  }

  $("#js-submit-" + drId).hide();
  $("#submit-" + drId).show();
  $("#submit-" + drId).trigger("click");
}

function myalert(
  message,
  bgColor = "danger",
  textColor = "white",
  alertEl = ".myalert-middle-bottom",
  timeOut = 5000
) {
  let randomId = Math.floor(Math.random() * 1000);
  $(alertEl).append(
    `<div id="alert-` +
    randomId +
    `" class="shadow rounded m-2 p-3 bg-` +
    bgColor +
    ` text-` +
    textColor +
    `"> <span class="">` +
    message +
    `</span> </div>`
  );
  setTimeout(() => {
    $("#alert-" + randomId).fadeOut(500, () => {
      $(this).remove();
    });
  },
    timeOut);
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// App Setting Page

function addColorAppSetting(e) {

  let variable = $(e).attr('var')

  let addToId = $(e).attr('add-to')
  let addToIdSelector = '#' + $(e).attr('add-to')

  let childrenCount = parseInt(
    $(addToIdSelector).children().length
  );

  // add removeBtn if one color exists
  if (childrenCount == 1) {
    if (!$(`#${variable}-btn-1`).length)
      $(`#${addToId}-1`).append(`<div id="${variable}-btn-1" remove="${addToId}-1" class="remove-color-btn bg-danger rounded px-2" onclick="removeColorAppSetting('#${addToId}-1', '${addToId}', '${variable}')">X</div>`)
  }

  // add Color
  $(addToIdSelector).append(`<div id="${addToId}-${childrenCount + 1}" class="d-flex m-2">
    <input type="color" name="color_${variable}_${childrenCount + 1}" id="${variable}_${childrenCount + 1}" class="color-input mx-1 w-100">
    <div id="${variable}-btn-${childrenCount + 1}" remove="${addToId}-${childrenCount + 1}" class="remove-color-btn bg-danger rounded px-2" onclick="removeColorAppSetting('#${addToId}-${childrenCount + 1}', '${addToId}', '${variable}')">
    X
    </div>
    </div>`)

}

function removeColorAppSetting(el, father, variable) {


  $(el).remove()

  // set ids and ...
  let i = 1
  $(`#${father}`).children('div').each(function() {
    // تغییر آیدی خودش
    $(this).attr('id', father + '-' + i)

    // تغییر آیدی بچه هاش و محتویات بچه هاش
    $(this).children('input').attr('name', `color_${variable}_${i}`)
    $(this).children('input').attr('id', `${variable}_${i}`)

    $(this).children('div').attr('remove', `${father}-${i}`)
    $(this).children('div').attr('onclick', `removeColorAppSetting('#${father}-${i}', '${father}', '${variable}')`)

    i++;

  });


  // remove RemoveBtn if one color exists
  let childrenCount = parseInt(
    $(`#${father}`).children().length
  );

  if (childrenCount <= 1) {
    $(`#${father} > div > div`).remove()
  }

}

function checkSliderLinkToSelect(e, index) {
  let linkToVal = $(e).val()

  if (linkToVal === 'announcement-page') {
    $('#mainSlider-announcement-page-' + index).append(`<div id="mainSlider-announcement-page-added-${index}">
      <div class="d-flex mb-1">
      <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">عنوان</span>
      <input type="text" name="imageSlider_mainScreenImageSlider_announcementTitle_${index}" id="imageSlider_mainScreenImageSlider_announcementTitle_${index}" class="form-control mx-1 w-100">
      </div>
      <div>
      <textarea name="imageSlider_mainScreenImageSlider_announcementContent_${index}" id="imageSlider_mainScreenImageSlider_announcementContent_${index}" rows="4" class="form-control w-100"></textarea>
      </div>
      </div>`)
    // ckeditor HTML editor for textarea
    CKEDITOR.replace('imageSlider_mainScreenImageSlider_announcementContent_' + index);
  } else {
    $('#mainSlider-announcement-page-added-' + index).remove()
  }
}

function removeSlider(el, index) {
  $(el).remove()
  let fatherChildrenCount = $('#mainSliders').children().length

  for (let i = index + 1; i <= fatherChildrenCount + 1; i++) {

    // slider box
    $('#mainSlider-' + i).attr('id', `mainSlider-${i - 1}`)

    // slider title
    $('#sliderTitle-' + i).attr('id', `sliderTitle-${i - 1}`)
    $(`#sliderTitle-${i - 1}`).html(i - 1);

    // remove btn
    $(`#removeSlider-${i}`).attr('id', `removeSlider-${i - 1}`)
    $(`#removeSlider-${i - 1}`).attr('onclick', `removeSlider('#mainSlider-${i - 1}', ${i - 1})`)

    // input: url
    $(`#imageSlider_mainScreenImageSlider_url_${i}`).attr('id', `imageSlider_mainScreenImageSlider_url_${i - 1}`)
    $(`#imageSlider_mainScreenImageSlider_url_${i - 1}`).attr('name', `imageSlider_mainScreenImageSlider_url_${i - 1}`)

    // select
    $(`#imageSlider_mainScreenImageSlider_linkTo_${i}`).attr('id', `imageSlider_mainScreenImageSlider_linkTo_${i - 1}`)
    $(`#imageSlider_mainScreenImageSlider_linkTo_${i - 1}`).attr('name', `imageSlider_mainScreenImageSlider_linkTo_${i - 1}`)
    $(`#imageSlider_mainScreenImageSlider_linkTo_${i - 1}`).attr('onchange', `checkSliderLinkToSelect(this, ${i - 1})`)


    /////////////////////////////////// announcement
    // input announcement box
    if ($(`#mainSlider-announcement-page-${i}`).length)
      $(`#mainSlider-announcement-page-${i}`).attr('id', `mainSlider-announcement-page-${i - 1}`)

    // input announcement box
    if ($(`#mainSlider-announcement-page-added-${i}`).length)
      $(`#mainSlider-announcement-page-added-${i}`).attr('id', `mainSlider-announcement-page-added-${i - 1}`)


    // input announcement title
    if ($(`#imageSlider_mainScreenImageSlider_announcementTitle_${i}`).length) {
      $(`#imageSlider_mainScreenImageSlider_announcementTitle_${i}`).attr('id', `imageSlider_mainScreenImageSlider_announcementTitle_${i - 1}`)
      $(`#imageSlider_mainScreenImageSlider_announcementTitle_${i - 1}`).attr('name', `imageSlider_mainScreenImageSlider_announcementTitle_${i - 1}`)
    }

    // input announcement content
    if ($(`#imageSlider_mainScreenImageSlider_announcementContent_${i}`).length) {
      $(`#imageSlider_mainScreenImageSlider_announcementContent_${i}`).attr('id', `imageSlider_mainScreenImageSlider_announcementContent_${i - 1}`)
      $(`#imageSlider_mainScreenImageSlider_announcementContent_${i - 1}`).attr('name', `imageSlider_mainScreenImageSlider_announcementContent_${i - 1}`)
    }

  }

}