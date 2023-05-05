let row = 1;
document.getElementById("append-document-form").addEventListener("click", function (e) {
    e.preventDefault();
    let new_line = document.createElement("tr");
    let nextItemId = this.getAttribute('data-next-id');
    new_line.innerHTML = '<td class="p-1 m-0"> <input type="checkbox" class="form-check" value="new"> </td> <td class="w-50 p-1 m-0">  <input type="text" id="invoiceDescription_' + nextItemId + '" name="description[]" class="form-control form-control-sm invoiceItem1" placeholder="Add description" required onkeyup="checkInvoiceItemsIsModified()"> </td><td class="p-1 m-0"><select name="invoiceItemTaxLevel[]" id="invoiceItemTaxLevel_' + nextItemId + '" class="select form-select invoiceItemTaxLevel" onchange="calculateInvoiceTax(this)" data-current-item-id="' + nextItemId + '"><option value="">None</option><option value="1">L1 (10%)</option><option value="2">L2 (20%)</option><option value="3">L3 (30%)</option></select></td><td class="text-center"><span class="invoiceItemTax" id="invoiceItemTax_' + nextItemId + '"></span></td><td class="text-center"><span  class="invoiceItemDiscount" id="invoiceItemDiscount_' + nextItemId + '"></span></td> <td class="p-1 m-0"><input type="number"  id="invoiceAmount_' + nextItemId + '" required name="amount[]" class="form-control form-control-sm item_amount invoiceItem2" onkeyup="calculateTotal(this), checkInvoiceItemsIsModified()" data-current-item="' + nextItemId + '" placeholder="Amount" step="0.01"></td><input type="hidden" value = "new_' + row + '" name="invoiceItemId[]"><td><p class="remove_invoice_item text-center cursor-pointer w-10 h-10 p-1 m-0" onclick="removeInvoiceItem(this)"><i class="ph-trash text-danger"></i></p></td>';
    document.querySelector(".add_new_line").appendChild(new_line.cloneNode(true));
    row++;
    this.setAttribute('data-next-id', parseInt(nextItemId) + 1);
    checkInvoiceItemsIsModified();
});

function removeInvoiceItem(item)
{
    item.parentNode.parentNode.remove();
    row--;
    checkInvoiceItemsIsModified();
    calculateTotal();
}
const calculateTotal = (target = null) => {
    let currentItem;
    let discountElement;
    if (target) {
        currentItem = target.getAttribute('data-current-item');
        discountElement = document.getElementById("invoiceItemDiscount_" + currentItem);
    }
    let amountItems = [];
    document.querySelectorAll('.invoiceItem2').forEach(el => {
        amountItems.push(el.value)
    });

    let amountTotal = amountItems.reduce((a,b) => {
        return parseFloat(a) + parseFloat(b);
    });

    let transactions = [];
    document.querySelectorAll('.transaction_amount').forEach(el => {
        transactions.push(el.value);
    });

    let totalTransactionAmount = 0;
    if (transactions.length > 0) {
        totalTransactionAmount = transactions.reduce((a,b) => {
            return parseFloat(a) + parseFloat(b);
            });
    }

    let invoiceCurrencyCode = document.getElementById("currency_currency").value;
    let invoiceCurrency = _all_currencies[invoiceCurrencyCode];
    let currencyFormat = currency_formatter(invoiceCurrency.precision, invoiceCurrency.prefix, invoiceCurrency.suffix);

    let discountRate = document.getElementById("option_discountRate").value;
    let credit = document.getElementById("invoice_credit").value;
    let invoiceTotal = parseFloat(amountTotal);
    let invoiceTotalDiscount = 0;
    let invoiceTotalTax = 0;
    discountElement ? discountElement.innerText = parseFloat((parseFloat(target.value) * parseFloat(discountRate)) / 100).toFixed(2) : false;

    let discountArray = [];
    document.querySelectorAll('.invoiceItemDiscount').forEach((item) => {
        discountArray.push(item.value);
    });

    let taxArray = [];
    document.querySelectorAll('.invoiceItemTax').forEach((item) => {
        taxArray.push(item.value);
    })

    invoiceTotalTax = taxArray.reduce((a,b) => {
        return parseFloat(a) + parseFloat(b);
    });

    invoiceTotalDiscount = discountArray.reduce((a,b) => {
        return parseFloat(a) + parseFloat(b);
    });

    let invoiceTotalWithTax = invoiceTotal + parseFloat(invoiceTotalTax);
    let discountableAmount = parseFloat(invoiceTotalDiscount) + parseFloat(credit);
    let totalInvoiceAmount = invoiceTotalWithTax - discountableAmount;
    let dueTotal = totalInvoiceAmount - parseFloat(totalTransactionAmount);

    document.getElementById("discount_row").style.display = invoiceTotalDiscount > 0 ? '' : 'none';
    document.getElementById("tax_row").style.display = invoiceTotalTax > 0 ? '' : 'none';
    document.getElementById("credit_row").style.display = credit > 0 ? '' : 'none';

    document.getElementById("subTotal").innerText = currencyFormat.format(invoiceTotal);
    document.getElementById("dueTotal").innerText = currencyFormat.format(dueTotal);
    document.getElementById("discount").innerText = currencyFormat.format(invoiceTotalDiscount);
    document.getElementById("tax").innerText = currencyFormat.format(invoiceTotalTax);
    document.getElementById("credit").innerText = currencyFormat.format(credit);
    document.getElementById("total").innerText = currencyFormat.format(totalInvoiceAmount);
}


function calculateInvoiceTax(target)
{
    let currentItemId = parseInt(target.getAttribute('data-current-item-id'));
    let taxElement = document.getElementById("invoiceItemTax_" + currentItemId);
    let amountElement = document.getElementById("invoiceAmount_" + currentItemId);
    let discountElement = document.getElementById("invoiceItemDiscount_" + currentItemId);
    let taxLevelElementValue = parseInt(target.value);
    let taxableAmount = parseFloat(amountElement.value) - parseFloat(discountElement.innerText);

    let taxablePercentage = 0;
    if (taxLevelElementValue === 1) {
        taxablePercentage = parseFloat(document.getElementById('option_taxRate').value);
    } else if (taxLevelElementValue === 2) {
        taxablePercentage = parseFloat(document.getElementById('option_tax2Rate').value);
    } else if (taxLevelElementValue === 3) {
        taxablePercentage = parseFloat(document.getElementById('option_tax3Rate').value);
    }
    taxElement.innerText = parseFloat((taxableAmount * taxablePercentage) / 100).toFixed(2);
    checkInvoiceItemsIsModified();
}

