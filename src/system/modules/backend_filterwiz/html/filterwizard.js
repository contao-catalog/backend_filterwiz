
/**
 * Class BackendFilterWizard
 *
 * Provide methods to handle back end tasks.
 * @copyright  Thyon Design
 * @author     John Brand <john.brand@thyon.com>
 * @package    BackendFilterWizard
 */
var BackendFilterWizard =
{

	/**
	 * List wizard
	 * @param object
	 * @param string
	 * @param string
	 */
	filterWizard: function(el, command, id)
	{
		var table = $(id);
		var tbody = table.getFirst().getNext();
		var parent = $(el).getParent().getParent();
		
		Backend.getScrollOffset();

		switch (command)
		{
			case 'up':
				parent.getPrevious() ? parent.injectBefore(parent.getPrevious()) : parent.injectInside(tbody);
				break;

			case 'down':
				parent.getNext() ? parent.injectAfter(parent.getNext()) : parent.injectBefore(tbody.getFirst());
				break;

		}
		

		rows = tbody.getChildren();

		for (var i=0; i<rows.length; i++)
		{
			var childs = rows[i].getChildren();

			for (var j=0; j<childs.length; j++)
			{
				var first = childs[j].getFirst();

				if (first && (first.type == 'checkbox' || first.type == 'radio'))
				{
					first.name = first.name.replace(/\[[0-9]+\]/ig, '[' + i + ']');
				}

			}
		}

				
	}
	
}
