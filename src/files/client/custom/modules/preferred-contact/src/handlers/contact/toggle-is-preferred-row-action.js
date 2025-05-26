import RowActionHandler from 'handlers/row-action';

class ToggleIsPreferredRowAction extends RowActionHandler {

    isAvailable(model, action) {
        const hasAccountModel = model?.collection?.parentModel;
        const hasPreferredField = model.has?.('pcAccountIsPreferred');

        if (!hasAccountModel || !hasPreferredField) return false;

        const isPreferred = model.get('pcAccountIsPreferred');

        const actionMap = {
            pcSetPreferred: isPreferred === false,
            pcSetNotPreferred: isPreferred === true,
        };

        return actionMap[action] ?? false;
    }

    async process(model, action) {
        const accountId = model?.collection?.parentModel?.id;
        const contactId = model?.id;

        if (!accountId || !contactId) {
            console.error('Cannot toggle: missing account or contact ID.');
            return;
        }

        Espo.Ui.notifyWait();

        try {
            await Espo.Ajax.postRequest(`Contact/${contactId}/toggleIsPreferred`, {
                id: accountId
            });

            Espo.Ui.success(this.view.translate('Modified'));
            model.collection.parentModel.trigger('update-related:contacts');

        } catch (error) {
            console.error('Toggle failed:', error);
            Espo.Ui.error(this.view.translate('Error'));
        }
    }
}

// noinspection JSUnusedGlobalSymbols
export default ToggleIsPreferredRowAction;
