import axios from 'axios';

const CrudMixin = {
	data() {
		return {
			items: [],
			// { all:10, trash:3 }
			counts: {},
			pagination: {},
			currentPage: 1,
		}
	},
	methods: {
		paginate(page) {
			this.currentPage = page;
			this.get_items();
		},
		get_items(url, config = {}) {
			return new Promise((resolve, reject) => {
				axios.get(url, config).then(response => {
					let _data = response.data.data;
					this.items = _data.items;
					this.counts = _data.counts;
					this.pagination = _data.pagination;
					resolve(_data);
				}).catch(error => {
					reject(error);
				})
			});
		},
		get_item(url, config = {}) {
			return new Promise((resolve, reject) => {
				axios.get(url, config).then(response => {
					resolve(response);
				}).catch(error => {
					reject(error);
				})
			});
		},
		create_item(url, data = [], config = {}) {
			return new Promise((resolve, reject) => {
				axios.post(url, data, config).then(response => {
					resolve(response.data.data);
				}).catch(error => {
					reject(error);
				})
			});
		},
		update_item(url, data = [], config = {}) {
			return new Promise((resolve, reject) => {
				axios.put(url, data, config).then(response => {
					resolve(response.data.data);
				}).catch(error => {
					reject(error);
				})
			});
		},
		delete_item(url, config = {}) {
			return new Promise((resolve, reject) => {
				axios.delete(url, config).then(response => {
					resolve(response.data.data);
				}).catch(error => {
					reject(error);
				})
			});
		},
		action_trash(url, id, action) {
			let validActions = ['trash', 'restore', 'delete'];
			if (-1 === validActions.indexOf(action)) {
				console.log('Only trash, restore and delete are supported.');
				return;
			}
			return new Promise((resolve, reject) => {
				axios
					.post(url, {id: id, action: action})
					.then((response) => {
						resolve(response.data.data)
					})
					.catch((error) => {
						reject(error);
					});
			});
		},
		action_batch_trash(url, ids, action) {
			let validActions = ['trash', 'restore', 'delete'];
			if (-1 === validActions.indexOf(action)) {
				console.log('Only trash, restore and delete are supported.');
				return;
			}
			return new Promise((resolve, reject) => {
				axios
					.post(url, {ids: ids, action: action})
					.then((response) => {
						resolve(response.data.data);
					})
					.catch((error) => {
						reject(error);
					});
			});
		}
	}
};

export {CrudMixin}
