              function domo(){

                var events = [
                "Enter",
                "PageUp",
                "PageDown",
                "Space",

                ];

                // the fetching...
                $.each(elements, function(i, e) { // i is element index. e is element as text.
                 var newElement = ( /[\+]+/.test(elements[i]) ) ? elements[i].replace("+","_") : elements[i];

                   // Binding keys
                   $('input, textarea, body').bind('keydown', elements[i], function assets(e) {

                    switch (e.data.keys)
                    {
                      case 'Shift+f3':
                        // var text=$(this).val();
                        var input=this;
                        var selectionStart=input.selectionStart;
                        var selectionEnd=input.selectionEnd;
                        if(selectionStart!=selectionEnd)
                        {

                          var text = input.value.substr(input.selectionStart, input.selectionEnd - input.selectionStart);
                          var starttext=input.value.substr(0, input.selectionStart);
                          var endtext=input.value.substr(input.selectionEnd, input.textLength);
                          debugger;
                          switch (text)
                          {
                           case text.toLowerCase():
                           text=toTitleCase(text);
                           break;
                           case text.toUpperCase():
                           text=text.toLowerCase();
                           break;
                           case toTitleCase(text):
                           text=text.toUpperCase();
                           break;
                           default: 
                           text=text.toLowerCase();

                         }
                         input.value=starttext + text + endtext;
                         input.selectionStart=selectionStart;
                         input.selectionEnd=selectionEnd;
                       }
                       else
                       {
                        var text = input.value;
                        debugger;
                        switch (text)
                        {
                         case text.toLowerCase():
                         text=toTitleCase(text);
                         break;
                         case text.toUpperCase():
                         text=text.toLowerCase();
                         break;
                         case toTitleCase(text):
                         text=text.toUpperCase();
                         break;
                         default: 
                         text=text.toLowerCase();

                       }
                       input.value=text;
                     }
                     break;
                     case 'Ctrl+1':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[0]);
                     break;
                     case 'Ctrl+2':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[1]);
                     break;
                     case 'Ctrl+3':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[2]);
                     break;
                     case 'Ctrl+4':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[3]);
                     break;
                     case 'Ctrl+5':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[4]);
                     break;
                     case 'Ctrl+6':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[5]);
                     break;
                     case 'Ctrl+7':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[6]);
                     break;
                     case 'Ctrl+8':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[7]);
                     break;
                     case 'Ctrl+9':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[8]);
                     break;
                     case 'Ctrl+0':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[9]);
                     break;
                     case 'Ctrl+-':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[10]);
                     break;
                     case 'Ctrl+=':
                     var oldtext=$(this).val();
                     $(this).val(oldtext + '\r\n' + mortgagewordings[11]);
                     break;
                     case 'Ctrl+Shift+s':
                     $('#save').click();
                     $('#save').attr('id', 'non-clicable');
                     setTimeout(function(){
                      $('#non-clicable').attr('id', 'save');
                     }, 1000)
                     break;

                   }
                   return false;
                 });
            });

            }

            function toTitleCase(str) {
              return str.replace(/(?:^|\s)\w/g, function(match) {
                return match.toUpperCase();
              });
            }