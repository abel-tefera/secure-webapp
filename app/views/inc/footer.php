</div>
<script>
    $('#addComplaintForm').submit(function(event) {
        event.preventDefault();
        var title = $('#title').val();
        var complaintBody = $('#complaintBody').val();

        grecaptcha.ready(function() {
            grecaptcha.execute('6Ld6XZMcAAAAABuUHbT2toqqdCrUq3KwItvRPvJh', {
                action: 'complaintAdd'
            }).then(function(token) {
                $('#addComplaintForm').prepend('<input type="hidden" name="reCtoken" value="' + token + '">');
                $('#addComplaintForm').prepend('<input type="hidden" name="reCaction" value="complaintAdd">');
                $('#addComplaintForm').unbind('submit').submit();
            });;
        });
    });
</script>
</body>

</html>